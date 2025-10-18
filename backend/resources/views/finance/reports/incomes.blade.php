@extends('layouts.app')
@section('content')
<h2 class="text-xl font-semibold mb-4">Laporan Penerimaan</h2>

<form method="GET" class="mb-4 flex gap-2 text-sm">
  <input type="date" name="from" value="{{ $from }}" class="border rounded px-3 py-2" />
  <input type="date" name="to" value="{{ $to }}" class="border rounded px-3 py-2" />
  <button class="px-3 py-2 bg-gray-100 rounded">Terapkan</button>
  <a href="{{ route('finance.reports.incomes') }}" class="px-3 py-2 underline">Reset</a>
  <span class="ml-auto flex gap-2">
    @php $qs = http_build_query(request()->only('from','to')); @endphp
    <a href="{{ route('finance.reports.incomes.csv') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export CSV</a>
    <a href="{{ route('finance.reports.incomes.xlsx') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export Excel</a>
    <a href="{{ route('finance.reports.incomes.pdf') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export PDF</a>
  </span>
  </form>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="bg-white rounded shadow p-4">
    <div class="font-medium mb-2">Per Kanal</div>
    <table class="min-w-full text-sm">
      <tbody>
        @forelse($byChannel as $ch=>$tot)
          <tr class="border-t"><td class="py-2 pr-4 uppercase">{{ $ch }}</td><td class="py-2 pr-4 text-right">Rp {{ number_format($tot,0,',','.') }}</td></tr>
        @empty
          <tr><td class="py-4 text-gray-500">Tidak ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="bg-white rounded shadow p-4">
    <div class="font-medium mb-2">Per Program (Top)</div>
    <table class="min-w-full text-sm">
      <tbody>
        @forelse($byProgram as $row)
          @php $name = $row->pid ? ($programNames[$row->pid] ?? 'Program #'.$row->pid) : 'General Fund'; @endphp
          <tr class="border-t"><td class="py-2 pr-4">{{ $name }}</td><td class="py-2 pr-4 text-right">Rp {{ number_format($row->total,0,',','.') }}</td></tr>
        @empty
          <tr><td class="py-4 text-gray-500">Tidak ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
