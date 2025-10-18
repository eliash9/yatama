@extends('layouts.app')
@section('content')
@include('partials.flash')

<h2 class="text-xl font-semibold mb-4">Realisasi vs Anggaran</h2>

<form method="GET" class="mb-4 flex flex-wrap gap-2 text-sm items-center">
  <select name="unit_id" class="border rounded px-3 py-2">
    <option value="">Pilih Unit</option>
    @foreach($units as $u)
      <option value="{{ $u->id }}" @selected((int)($unitId)=== (int)$u->id)>{{ $u->name }}</option>
    @endforeach
  </select>
  <select name="periode_id" class="border rounded px-3 py-2">
    <option value="">Pilih Periode</option>
    @foreach($periodes as $p)
      <option value="{{ $p->id }}" @selected((int)($periodeId)=== (int)$p->id)>{{ $p->name }}</option>
    @endforeach
  </select>
  <button class="px-3 py-2 bg-gray-100 rounded">Terapkan</button>
  <a href="{{ route('finance.reports.budget_realization') }}" class="px-3 py-2 underline">Reset</a>
  @php $qs = http_build_query(request()->only('unit_id','periode_id')); @endphp
  <span class="ml-auto flex gap-2">
    <a href="{{ route('finance.reports.budget_realization') }}.xlsx?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export Excel</a>
    <a href="{{ route('finance.reports.budget_realization') }}.pdf?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export PDF</a>
  </span>
  <span class="text-gray-400">&nbsp;</span>
  </form>

<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="text-gray-600">
      <tr class="text-left">
        <th class="py-2 px-3">Kode Akun</th>
        <th class="py-2 px-3 text-right">Pagu</th>
        <th class="py-2 px-3 text-right">Realisasi</th>
        <th class="py-2 px-3 text-right">Sisa</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $r)
        <tr class="border-t">
          <td class="py-2 px-3 font-mono">{{ $r['account_code'] }}</td>
          <td class="py-2 px-3 text-right">Rp {{ number_format($r['pagu'],0,',','.') }}</td>
          <td class="py-2 px-3 text-right">Rp {{ number_format($r['realisasi'],0,',','.') }}</td>
          <td class="py-2 px-3 text-right">Rp {{ number_format($r['sisa'],0,',','.') }}</td>
        </tr>
      @empty
        <tr><td colspan="4" class="py-6 text-center text-gray-500">Pilih unit dan periode untuk melihat data</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
