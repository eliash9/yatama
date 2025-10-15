@extends('layouts.app')
@section('content')
<h2 class="text-xl font-semibold mb-4">Realisasi Kampanye/Program</h2>

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

