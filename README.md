Sistem Informasi Manajemen Dana (Yatama)

Ringkas: repositori ini berisi spesifikasi, rancangan data, dan rancangan API untuk sistem manajemen dana (penganggaran, pengajuan, persetujuan, pencairan, transaksi, dan pelaporan). Implementasi teknis (framework/DB) menunggu preferensi Anda.

Struktur dokumen:
- `docs/01-visi-fitur.md` – tujuan, manfaat, cakupan fitur inti
- `docs/02-model-data.md` – entitas, relasi, dan skema awal
- `docs/03-api.md` – rancangan REST API dan contoh payload
- `docs/04-workflow.md` – alur proses dan state machine
- `docs/05-peran-hak-akses.md` – peran dan matriks izin

Langkah selanjutnya yang direkomendasikan:
- Tentukan stack: mis. Laravel + MySQL, Django + PostgreSQL, atau Next.js + Prisma.
- Setelah dipilih, saya akan scaffold proyek minimal dan memetakan skema + API ke stack tersebut.

