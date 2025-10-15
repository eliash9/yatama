Alur Proses & Status

Pengajuan Dana (State Machine)
- draft → diajukan → ditinjau(level 1..N) → disetujui → dicairkan → selesai
- Penolakan: ditinjau → ditolak (bisa kembali ke draft untuk revisi)

Detail Tahap
- Draft: pemohon mengisi data dan item, bisa simpan sementara.
- Diajukan: terkunci dari edit tertentu; notifikasi ke approver level 1.
- Ditinjau: setiap level memutuskan approve/reject dengan catatan.
- Disetujui: semua level terpenuhi; siap dibuatkan dokumen pencairan.
- Dicairkan: dilakukan pencairan sebagian/sekali; transaksi kas/bank tercatat.
- Selesai: semua proses tuntas; hanya baca (read-only) kecuali admin.

Aturan Transisi
- Submit hanya dari draft dan validasi data terpenuhi.
- Approve level k hanya boleh jika level k-1 sudah approve.
- Reject akan menutup siklus persetujuan dan kembali ke draft (opsi revisi).
- Pencairan hanya saat status disetujui; total tidak boleh melebihi diminta.

Notifikasi
- Saat diajukan: kirim ke approver level 1.
- Saat approve/reject: kirim ke pemohon dan level berikutnya (jika ada).
- Saat dicairkan: kirim ringkasan pencairan ke pemohon dan bendahara.

Audit Trail (Contoh)
- pengajuan.create, pengajuan.submit, approval.decide, pengajuan.update,
  pencairan.create, transaksi.create, lampiran.upload

