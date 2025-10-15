<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>E-Receipt {{ $row->receipt_no }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-900">
  <div class="max-w-2xl mx-auto p-6">
    <div class="flex items-start justify-between border-b pb-4 mb-4">
      <div>
        <div class="text-xl font-semibold">E-Receipt</div>
        <div class="text-gray-500">Sistem Informasi Manajemen Dana</div>
      </div>
      <div class="text-right">
        <div class="font-mono">No: {{ $row->receipt_no }}</div>
        <div>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</div>
      </div>
    </div>
    <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
      <div><dt class="text-gray-500">Donatur</dt><dd>{{ $row->donor->name ?? '-' }}</dd></div>
      <div><dt class="text-gray-500">Kanal</dt><dd class="uppercase">{{ $row->channel }}</dd></div>
      <div><dt class="text-gray-500">Program</dt><dd>{{ $row->program->name ?? 'General Fund' }}</dd></div>
      <div><dt class="text-gray-500">Jumlah</dt><dd>Rp {{ number_format($row->amount,0,',','.') }}</dd></div>
      <div class="md:col-span-2"><dt class="text-gray-500">Catatan</dt><dd>{{ $row->notes ?? '-' }}</dd></div>
    </dl>
    <div class="mt-8 text-xs text-gray-500">Dokumen ini dihasilkan secara elektronik. Silakan simpan atau cetak untuk arsip Anda.</div>
    <div class="mt-4">
      <button onclick="window.print()" class="px-3 py-2 bg-blue-600 text-white rounded">Cetak / Simpan PDF</button>
    </div>
  </div>
</body>
</html>

