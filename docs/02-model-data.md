Model Data (Konseptual)

Entitas Utama
- user: akun pengguna sistem
  - id, name, email, password_hash, status, last_login_at, created_at, updated_at
- role: peran akses (admin, bendahara, pimpinan, unit)
  - id, code, name, description
- user_role: relasi N:M user↔role
  - id, user_id, role_id
- unit: organisasi/unit kerja/program
  - id, code, name, parent_id?, is_active
- periode: periode anggaran (mis. 2025, Q1-2025)
  - id, code, name, start_date, end_date, is_locked
- anggaran: header anggaran per unit + periode
  - id, unit_id, periode_id, total_pagu, notes, status(draft|final)
- anggaran_item: rincian anggaran (per akun/pos)
  - id, anggaran_id, account_code, description, pagu, notes
- pengajuan: permintaan dana oleh unit
  - id, kode, unit_id, periode_id, pemohon_id, judul, deskripsi, total_diminta, status(draft|diajukan|ditinjau|disetujui|ditolak|dicairkan|selesai), submitted_at
- pengajuan_item: rincian pengajuan yang mengacu pos anggaran
  - id, pengajuan_id, account_code, description, qty, unit_price, subtotal, anggaran_item_id?
- approval: jejak persetujuan tiap tingkat
  - id, pengajuan_id, approver_id, level, status(pending|approved|rejected), note, decided_at
- pencairan: data pencairan dana setelah disetujui
  - id, pengajuan_id, nomor_doc, tanggal, metode(cash|transfer), total_dicairkan, catatan
- transaksi: pencatatan kas/bank terkait pengajuan atau mandiri
  - id, tanggal, jenis(debit|kredit), akun_kas(bank|kas), amount, ref_type(pengajuan|lainnya), ref_id, memo
- lampiran: berkas terkait entitas (pengajuan, pencairan, transaksi)
  - id, ref_type, ref_id, filename, mime, size, url/path, uploader_id, uploaded_at
- audit_log: rekam perubahan/aksi
  - id, actor_id, action, ref_type, ref_id, changes(json), created_at

Relasi Kunci
- user N:M role (user_role)
- unit 1:N user (opsional: user.unit_id)
- periode 1:N anggaran; unit 1:N anggaran
- anggaran 1:N anggaran_item
- pengajuan terkait unit + periode, dan 1:N pengajuan_item
- pengajuan 1:N approval (multi-level)
- pengajuan 1:1..N pencairan (umumnya 1, bisa bertahap bila diperlukan)
- transaksi dapat mereferensi pengajuan (ref_type=pengajuan)

Catatan Implementasi
- Gunakan soft delete bila diperlukan, tetapi hindari untuk entitas keuangan final (auditability).
- Tambahkan indeks untuk kolom foreign key dan pencarian (code, status, tanggal).
- Simpan angka uang sebagai integer minor unit (mis. sen) untuk akurasi.
- Pertimbangkan constraint total realisasi <= pagu per anggaran_item.

Skema SQL Awal (PostgreSQL-ish)

CREATE TABLE users (
  id BIGSERIAL PRIMARY KEY,
  name TEXT NOT NULL,
  email TEXT UNIQUE NOT NULL,
  password_hash TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'active',
  unit_id BIGINT NULL,
  last_login_at TIMESTAMPTZ NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE roles (
  id BIGSERIAL PRIMARY KEY,
  code TEXT UNIQUE NOT NULL,
  name TEXT NOT NULL,
  description TEXT NULL
);

CREATE TABLE user_roles (
  id BIGSERIAL PRIMARY KEY,
  user_id BIGINT NOT NULL REFERENCES users(id),
  role_id BIGINT NOT NULL REFERENCES roles(id),
  UNIQUE(user_id, role_id)
);

CREATE TABLE units (
  id BIGSERIAL PRIMARY KEY,
  code TEXT UNIQUE NOT NULL,
  name TEXT NOT NULL,
  parent_id BIGINT NULL REFERENCES units(id),
  is_active BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE periodes (
  id BIGSERIAL PRIMARY KEY,
  code TEXT UNIQUE NOT NULL,
  name TEXT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  is_locked BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE anggarans (
  id BIGSERIAL PRIMARY KEY,
  unit_id BIGINT NOT NULL REFERENCES units(id),
  periode_id BIGINT NOT NULL REFERENCES periodes(id),
  total_pagu BIGINT NOT NULL DEFAULT 0,
  notes TEXT NULL,
  status TEXT NOT NULL DEFAULT 'draft',
  UNIQUE(unit_id, periode_id)
);

CREATE TABLE anggaran_items (
  id BIGSERIAL PRIMARY KEY,
  anggaran_id BIGINT NOT NULL REFERENCES anggarans(id) ON DELETE CASCADE,
  account_code TEXT NOT NULL,
  description TEXT NOT NULL,
  pagu BIGINT NOT NULL DEFAULT 0,
  notes TEXT NULL
);

CREATE TABLE pengajuans (
  id BIGSERIAL PRIMARY KEY,
  kode TEXT UNIQUE NOT NULL,
  unit_id BIGINT NOT NULL REFERENCES units(id),
  periode_id BIGINT NOT NULL REFERENCES periodes(id),
  pemohon_id BIGINT NOT NULL REFERENCES users(id),
  judul TEXT NOT NULL,
  deskripsi TEXT NULL,
  total_diminta BIGINT NOT NULL DEFAULT 0,
  status TEXT NOT NULL DEFAULT 'draft',
  submitted_at TIMESTAMPTZ NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE pengajuan_items (
  id BIGSERIAL PRIMARY KEY,
  pengajuan_id BIGINT NOT NULL REFERENCES pengajuans(id) ON DELETE CASCADE,
  account_code TEXT NOT NULL,
  description TEXT NOT NULL,
  qty NUMERIC(18,2) NOT NULL DEFAULT 1,
  unit_price BIGINT NOT NULL DEFAULT 0,
  subtotal BIGINT NOT NULL DEFAULT 0,
  anggaran_item_id BIGINT NULL REFERENCES anggaran_items(id)
);

CREATE TABLE approvals (
  id BIGSERIAL PRIMARY KEY,
  pengajuan_id BIGINT NOT NULL REFERENCES pengajuans(id) ON DELETE CASCADE,
  approver_id BIGINT NOT NULL REFERENCES users(id),
  level INT NOT NULL,
  status TEXT NOT NULL DEFAULT 'pending',
  note TEXT NULL,
  decided_at TIMESTAMPTZ NULL,
  UNIQUE(pengajuan_id, level)
);

CREATE TABLE pencairans (
  id BIGSERIAL PRIMARY KEY,
  pengajuan_id BIGINT NOT NULL REFERENCES pengajuans(id) ON DELETE CASCADE,
  nomor_doc TEXT NOT NULL,
  tanggal DATE NOT NULL,
  metode TEXT NOT NULL,
  total_dicairkan BIGINT NOT NULL,
  catatan TEXT NULL
);

CREATE TABLE transaksi (
  id BIGSERIAL PRIMARY KEY,
  tanggal DATE NOT NULL,
  jenis TEXT NOT NULL,
  akun_kas TEXT NOT NULL,
  amount BIGINT NOT NULL,
  ref_type TEXT NULL,
  ref_id BIGINT NULL,
  memo TEXT NULL
);

CREATE TABLE lampirans (
  id BIGSERIAL PRIMARY KEY,
  ref_type TEXT NOT NULL,
  ref_id BIGINT NOT NULL,
  filename TEXT NOT NULL,
  mime TEXT NOT NULL,
  size BIGINT NOT NULL,
  url TEXT NOT NULL,
  uploader_id BIGINT NOT NULL REFERENCES users(id),
  uploaded_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE audit_logs (
  id BIGSERIAL PRIMARY KEY,
  actor_id BIGINT NULL REFERENCES users(id),
  action TEXT NOT NULL,
  ref_type TEXT NULL,
  ref_id BIGINT NULL,
  changes JSONB NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

