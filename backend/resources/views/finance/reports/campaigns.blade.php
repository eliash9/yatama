@extends('layouts.app')
@section('content')
<h2 class="text-xl font-semibold mb-4">Realisasi Kampanye/Program</h2>

@php $qs = http_build_query(request()->query()); @endphp
<div class="mb-4 flex gap-2 text-sm">
  <a href="{{ route('finance.reports.campaigns.xlsx') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export Excel</a>
  <a href="{{ route('finance.reports.campaigns.pdf') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export PDF</a>
</div>

<div class="bg-white rounded shadow divide-y">
  @forelse($programs as $p)
    @php $target = (int)($p->target_amount ?? 0); $total = (int)($totals[$p->id] ?? 0); $pct = $target ? min(100, round($total/$target*100)) : null; @endphp
    <div class="p-4">
      <div class="flex items-center justify-between">
        <div>
          <div class="font-medium">{{ $p->name }}</div>
          <div class="text-xs text-gray-500">Target: {{ $target ? ('Rp '.number_format($target,0,',','.')) : '-' }}</div>
        </div>
        <div class="text-sm">Terkumpul: Rp {{ number_format($total,0,',','.') }}</div>
      </div>
      <div class="mt-2 h-2 bg-gray-100 rounded">
        @if($pct !== null)
        <div class="h-2 bg-blue-600 rounded" style="width: {{ $pct }}%"></div>
        @endif
      </div>
    </div>
  @empty
    <div class="p-4 text-gray-500">Belum ada program</div>
  @endforelse
</div>
@endsection
