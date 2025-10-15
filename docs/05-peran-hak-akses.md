Peran & Hak Akses (Ringkas)

Peran
- admin: kendali penuh konfigurasi dan audit, tidak ikut approval operasional.
- bendahara: kelola anggaran, pencairan, transaksi, laporan.
- pimpinan: approver sesuai level kewenangan.
- unit: membuat pengajuan dan memonitor realisasi.

Matriks Izin (contoh)

Penganggaran
- admin: CRUD periode, unit, finalisasi anggaran.
- bendahara: CRUD anggaran & item, finalisasi (berdasarkan kebijakan).
- pimpinan: baca.
- unit: baca anggaran unitnya.

Pengajuan
- unit: CRUD draft, submit.
- pimpinan: approve/reject sesuai level.
- bendahara: baca untuk verifikasi; tidak memutuskan approval.
- admin: override terbatas (audit aware).

Pencairan & Transaksi
- bendahara: buat pencairan, catat transaksi.
- admin: konfigurasi akun kas/bank, laporan.
- unit/pimpinan: baca terkait pengajuannya.

Laporan
- semua peran: baca sesuai cakupan (unit/organisasi).

Catatan
- Implementasi bisa memakai RBAC berbasis role + policy per resource & action.
- Granular permission (per-unit/per-level) dapat ditambah via tabel mapping khusus.

