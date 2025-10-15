@extends('layouts.app')
@section('content')
<h2 class="text-xl font-semibold mb-4">Rasio Operasional</h2>

<form method="GET" class="mb-4 flex gap-2 text-sm">
  <input type="date" name="from" value="{{ $from }}" class="border rounded px-3 py-2" />
  <input type="date" name="to" value="{{ $to }}" class="border rounded px-3 py-2" />
  <button class="px-3 py-2 bg-gray-100 rounded">Terapkan</button>
  <a href="{{ route('finance.reports.operational_ratio') }}" class="px-3 py-2 underline">Reset</a>
</form>

@php
  $ops = (int)($ops ?? 0);
  $prog = (int)($programSpend ?? 0);
  $den = max(1, $ops + $prog);
  $ratio = round(($ops / $den) * 100, 2);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="bg-white rounded shadow p-4">
    <h3 class="font-medium mb-2">Ringkasan</h3>
    <ul class="text-sm">
      <li class="flex justify-between border-t py-2"><span>Pengeluaran Operasional</span><span>Rp {{ number_format($ops,0,',','.') }}</span></li>
      <li class="flex justify-between border-t py-2"><span>Pengeluaran Program</span><span>Rp {{ number_format($prog,0,',','.') }}</span></li>
      <li class="flex justify-between border-t py-2"><span>Rasio Operasional</span><span class="font-medium">{{ $ratio }}%</span></li>
    </ul>
  </div>
  <div class="bg-white rounded shadow p-4">
    <h3 class="font-medium mb-2">Visual</h3>
    <div class="h-3 bg-gray-100 rounded">
      <div class="h-3 bg-amber-500 rounded" style="width: {{ $ratio }}%"></div>
    </div>
    <div class="text-xs text-gray-500 mt-2">{{ $ratio }}% Operasional dari total pengeluaran (operasional + program).</div>
  </div>
</div>
@endsection

