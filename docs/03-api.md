Rancangan REST API

Konvensi
- Base URL: `/api/v1`
- Auth: Bearer JWT (placeholder; menunggu stack)
- Format: JSON, `created_at/updated_at` dalam ISO8601

Auth
- POST `/auth/login` { email, password } → { token, user }
- POST `/auth/logout` → 204
- GET `/auth/me` → { user, roles }

Referensi
- GET `/units` → daftar unit
- POST `/units` → buat unit
- PATCH `/units/:id` → ubah unit
- GET `/periodes` → daftar periode
- POST `/periodes` → buat periode

Anggaran
- GET `/anggarans?unit_id&periode_id` → list
- POST `/anggarans` { unit_id, periode_id, total_pagu, notes } → anggaran
- GET `/anggarans/:id` → detail + items
- POST `/anggarans/:id/items` → tambah item
- PATCH `/anggaran-items/:id` → ubah item
- POST `/anggarans/:id/finalize` → ubah status ke final (lock)

Pengajuan
- GET `/pengajuans?unit_id&status&periode_id` → list
- termasuk ringkasan total per status
- POST `/pengajuans` { unit_id, periode_id, judul, deskripsi, items[] } → pengajuan (draft)
- GET `/pengajuans/:id` → detail (items, approvals, lampiran)
- PATCH `/pengajuans/:id` → ubah data (selama draft/diajukan)
- POST `/pengajuans/:id/submit` → ubah status ke diajukan
- POST `/pengajuans/:id/attachments` (multipart) → unggah lampiran

Persetujuan
- GET `/pengajuans/:id/approvals` → riwayat
- POST `/pengajuans/:id/approvals` { action: approve|reject, note } → simpan keputusan di level aktif

Pencairan & Transaksi
- POST `/pengajuans/:id/pencairans` { nomor_doc, tanggal, metode, total_dicairkan, catatan }
- GET `/pencairans/:id` → detail
- POST `/transaksi` { tanggal, jenis, akun_kas, amount, ref_type?, ref_id?, memo }
- GET `/transaksi?from&to&akun_kas` → list

Laporan
- GET `/reports/realisasi?unit_id&periode_id` → { pagu, realisasi, sisa } per account_code
- GET `/reports/pengajuan?status&from&to&unit_id` → ringkasan pengajuan
- GET `/reports/arus-kas?from&to` → ringkasan debit/kredit per akun_kas

Contoh Payload

POST /pengajuans
{
  "unit_id": 10,
  "periode_id": 3,
  "judul": "Kegiatan Pelatihan A",
  "deskripsi": "Pengajuan untuk pelatihan A",
  "items": [
    { "account_code": "521211", "description": "Konsumsi", "qty": 50, "unit_price": 25000 },
    { "account_code": "521213", "description": "ATK", "qty": 1, "unit_price": 500000 }
  ]
}

Respons 201
{
  "id": 123,
  "kode": "PD-2025-000123",
  "status": "draft",
  "total_diminta": 1_750_000,
  "items": [ { "id": 999, "subtotal": 1_250_000 }, { "id": 1000, "subtotal": 500_000 } ]
}

Validasi & Aturan Utama
- Total `subtotal` items = `total_diminta` (server-side recompute).
- Saat submit: wajib ada minimal 1 item dan lampiran bila disyaratkan.
- Saat approve level N: pastikan level N-1 sudah approve.
- Saat pencairan: total_dicairkan <= total_diminta - total_dicairkan_sebelumnya.

