@extends('layouts.app')
@section('content')
<h2 class="text-xl font-semibold mb-4">Dana Terikat vs Umum</h2>

@php $qs = http_build_query(request()->only('from','to')); @endphp
<form method="GET" class="mb-4 flex gap-2 text-sm">
  <input type="date" name="from" value="{{ $from }}" class="border rounded px-3 py-2" />
  <input type="date" name="to" value="{{ $to }}" class="border rounded px-3 py-2" />
  <button class="px-3 py-2 bg-gray-100 rounded">Terapkan</button>
  <a href="{{ route('finance.reports.funds') }}" class="px-3 py-2 underline">Reset</a>
  <span class="ml-auto flex gap-2">
    <a href="{{ route('finance.reports.funds.xlsx') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export Excel</a>
    <a href="{{ route('finance.reports.funds.pdf') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export PDF</a>
  </span>
</form>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="bg-white rounded shadow p-4">
    <h3 class="font-medium mb-2">Masuk</h3>
    <ul class="text-sm">
      <li class="flex justify-between border-t py-2"><span>Earmark (Program)</span><span>Rp {{ number_format($earmark_in,0,',','.') }}</span></li>
      <li class="flex justify-between border-t py-2"><span>General Fund</span><span>Rp {{ number_format($general_in,0,',','.') }}</span></li>
    </ul>
  </div>
  <div class="bg-white rounded shadow p-4">
    <h3 class="font-medium mb-2">Keluar</h3>
    <ul class="text-sm">
      <li class="flex justify-between border-t py-2"><span>Program</span><span>Rp {{ number_format($earmark_out,0,',','.') }}</span></li>
      <li class="flex justify-between border-t py-2"><span>Umum/Operasional</span><span>Rp {{ number_format($general_out,0,',','.') }}</span></li>
    </ul>
  </div>
</div>
@endsection
