@extends('layouts.app')
@section('content')
<h2 class="text-xl font-semibold mb-4">Laporan Penyaluran</h2>

<form method="GET" class="mb-4 flex gap-2 text-sm">
  <input type="date" name="from" value="{{ $from }}" class="border rounded px-3 py-2" />
  <input type="date" name="to" value="{{ $to }}" class="border rounded px-3 py-2" />
  <button class="px-3 py-2 bg-gray-100 rounded">Terapkan</button>
  <a href="{{ route('finance.reports.disbursements') }}" class="px-3 py-2 underline">Reset</a>
  @php $qs = http_build_query(request()->only('from','to')); @endphp
  <span class="ml-auto flex gap-2">
    <a href="{{ route('finance.reports.disbursements.csv') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export CSV</a>
    <a href="{{ route('finance.reports.disbursements.xlsx') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export Excel</a>
    <a href="{{ route('finance.reports.disbursements.pdf') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export PDF</a>
  </span>
</form>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
  <div class="bg-white rounded shadow p-4">
    <div class="font-medium mb-2">Top Penerima</div>
    <table class="min-w-full text-sm">
      <tbody>
        @forelse($byBeneficiary as $b)
          <tr class="border-t"><td class="py-2 pr-4">ID #{{ $b->beneficiary_id }}</td><td class="py-2 pr-4">Rp {{ number_format($b->total,0,',','.') }}</td></tr>
        @empty
          <tr><td class="py-4 text-gray-500">Tidak ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="bg-white rounded shadow p-4">
    <div class="font-medium mb-2">Per Wilayah</div>
    <table class="min-w-full text-sm">
      <tbody>
        @forelse($byRegion as $r)
          <tr class="border-t"><td class="py-2 pr-4">{{ $r->region ?: '-' }}</td><td class="py-2 pr-4">Rp {{ number_format($r->total,0,',','.') }}</td></tr>
        @empty
          <tr><td class="py-4 text-gray-500">Tidak ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="text-gray-600"><tr class="text-left">
      <th class="py-2 px-3">Kode</th>
      <th class="py-2 px-3">Penerima</th>
      <th class="py-2 px-3">Program</th>
      <th class="py-2 px-3">Jumlah</th>
      <th class="py-2 px-3">Status</th>
      <th class="py-2 px-3">Tanggal</th>
    </tr></thead>
    <tbody>
      @forelse($rows as $row)
      <tr class="border-t">
        <td class="py-2 px-3 font-mono">{{ $row->code }}</td>
        <td class="py-2 px-3">{{ $row->beneficiary->name }}</td>
        <td class="py-2 px-3">{{ $row->program->name }}</td>
        <td class="py-2 px-3">Rp {{ number_format($row->amount,0,',','.') }}</td>
        <td class="py-2 px-3">{{ $row->status }}</td>
        <td class="py-2 px-3">{{ $row->created_at->format('d M Y') }}</td>
      </tr>
      @empty
      <tr><td colspan="6" class="py-6 text-center text-gray-500">Belum ada data</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $rows->links() }}</div>
@endsection
