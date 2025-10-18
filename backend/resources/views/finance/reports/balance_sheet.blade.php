@extends('layouts.app')
@section('content')
<h2 class="text-xl font-semibold mb-4">Laporan Posisi Keuangan (Neraca)</h2>

<form method="GET" class="mb-4 flex gap-2 text-sm items-center">
  <label>Per Tanggal</label>
  <input type="date" name="as_of" value="{{ $asOf }}" class="border rounded px-3 py-2" />
  <button class="px-3 py-2 bg-gray-100 rounded">Terapkan</button>
  <a href="{{ route('finance.reports.balance_sheet') }}" class="px-3 py-2 underline">Reset</a>
</form>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="bg-white rounded shadow p-4">
    <h3 class="font-medium mb-2">Aset</h3>
    <table class="min-w-full text-sm">
      <tbody>
        <tr class="border-t"><td class="py-2 pr-4">Kas & Bank</td><td class="py-2 pr-4 text-right">Rp {{ number_format($cashBank,0,',','.') }}</td></tr>
        <tr class="border-t font-medium"><td class="py-2 pr-4">Total Aset</td><td class="py-2 pr-4 text-right">Rp {{ number_format($assets,0,',','.') }}</td></tr>
      </tbody>
    </table>
  </div>

  <div class="bg-white rounded shadow p-4">
    <h3 class="font-medium mb-2">Kewajiban & Dana</h3>
    <table class="min-w-full text-sm">
      <tbody>
        <tr class="border-t"><td class="py-2 pr-4">Total Kewajiban</td><td class="py-2 pr-4 text-right">Rp {{ number_format($liabilities,0,',','.') }}</td></tr>
        <tr class="border-t"><td class="py-2 pr-4">Total Dana/Ekuitas</td><td class="py-2 pr-4 text-right">Rp {{ number_format($equity,0,',','.') }}</td></tr>
        <tr class="border-t font-medium"><td class="py-2 pr-4">Kewajiban + Dana</td><td class="py-2 pr-4 text-right">Rp {{ number_format($liabilities + $equity,0,',','.') }}</td></tr>
      </tbody>
    </table>
  </div>
</div>
@endsection

