@extends('layouts.app')
@section('content')
<h2 class="text-xl font-semibold mb-4">Arus Kas</h2>

<form method="GET" class="mb-4 flex gap-2 text-sm">
  <input type="date" name="from" value="{{ $from }}" class="border rounded px-3 py-2" />
  <input type="date" name="to" value="{{ $to }}" class="border rounded px-3 py-2" />
  <button class="px-3 py-2 bg-gray-100 rounded">Terapkan</button>
  <a href="{{ route('finance.reports.cashflow') }}" class="px-3 py-2 underline">Reset</a>
  @php $qs = http_build_query(request()->only('from','to')); @endphp
  <span class="ml-auto flex gap-2">
    <a href="{{ route('finance.reports.cashflow.csv') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export CSV</a>
    <a href="{{ route('finance.reports.cashflow.xlsx') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export Excel</a>
    <a href="{{ route('finance.reports.cashflow.pdf') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export PDF</a>
  </span>
</form>

<div class="bg-white rounded shadow p-4">
  <canvas id="cfChart" height="120"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  const cf = @json($series);
  const labels = cf.map(r => r.ym);
  const debit = cf.map(r => Number(r.debit));
  const kredit = cf.map(r => Number(r.kredit));
  new Chart(document.getElementById('cfChart'), {
    type: 'bar',
    data: { labels, datasets: [
      { label: 'Debit', data: debit, backgroundColor: '#22c55e' },
      { label: 'Kredit', data: kredit, backgroundColor: '#ef4444' }
    ]},
    options: { responsive: true, scales: { x: { stacked: true }, y: { stacked: true } } }
  });
</script>
@endsection
