@extends('layouts.app')
@section('content')
<h2 class="text-xl font-semibold mb-4">Laporan Aktivitas</h2>

<form method="GET" class="mb-4 flex gap-2 text-sm">
  <input type="date" name="from" value="{{ $from }}" class="border rounded px-3 py-2" />
  <input type="date" name="to" value="{{ $to }}" class="border rounded px-3 py-2" />
  <button class="px-3 py-2 bg-gray-100 rounded">Terapkan</button>
  <a href="{{ route('finance.reports.activity') }}" class="px-3 py-2 underline">Reset</a>
</form>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="bg-white rounded shadow p-4">
    <h3 class="font-medium mb-2">Sumber Dana</h3>
    <table class="min-w-full text-sm">
      <tbody>
        <tr class="border-t"><td class="py-2 pr-4">Donasi</td><td class="py-2 pr-4 text-right">Rp {{ number_format($donations,0,',','.') }}</td></tr>
        <tr class="border-t text-gray-500"><td class="py-2 pr-4">Usaha</td><td class="py-2 pr-4 text-right">Rp 0</td></tr>
        <tr class="border-t text-gray-500"><td class="py-2 pr-4">Hibah</td><td class="py-2 pr-4 text-right">Rp 0</td></tr>
        <tr class="border-t font-medium"><td class="py-2 pr-4">Total Sumber</td><td class="py-2 pr-4 text-right">Rp {{ number_format($totalSources,0,',','.') }}</td></tr>
      </tbody>
    </table>
  </div>
  <div class="bg-white rounded shadow p-4">
    <h3 class="font-medium mb-2">Penggunaan Dana</h3>
    <table class="min-w-full text-sm">
      <tbody>
        <tr class="border-t"><td class="py-2 pr-4">Bantuan/Program</td><td class="py-2 pr-4 text-right">Rp {{ number_format($programSpend,0,',','.') }}</td></tr>
        <tr class="border-t"><td class="py-2 pr-4">Operasional</td><td class="py-2 pr-4 text-right">Rp {{ number_format($operationalSpend,0,',','.') }}</td></tr>
        <tr class="border-t font-medium"><td class="py-2 pr-4">Total Penggunaan</td><td class="py-2 pr-4 text-right">Rp {{ number_format($totalUses,0,',','.') }}</td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="mt-6 bg-white rounded shadow p-4">
  <div class="flex justify-between text-sm">
    <div>Perubahan Bersih Aset Neto</div>
    <div class="font-medium">Rp {{ number_format($netChange,0,',','.') }}</div>
  </div>
  <p class="text-xs text-gray-500 mt-2">Catatan: kategori Usaha/Hibah belum ditrack eksplisit pada data saat ini.</p>
  <p class="text-xs text-gray-500">Donasi dihitung dari tabel Penerimaan; Penggunaan dari transaksi kredit (program dan operasional).</p>
  </div>
@endsection

