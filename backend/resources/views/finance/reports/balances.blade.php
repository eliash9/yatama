@extends('layouts.app')
@section('content')
<h2 class="text-xl font-semibold mb-4">Saldo Akun & Program</h2>

@php $qs = http_build_query(request()->query()); @endphp
<div class="mb-4 flex gap-2 text-sm">
  <a href="{{ route('finance.reports.balances.xlsx') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export Excel</a>
  <a href="{{ route('finance.reports.balances.pdf') }}?{{ $qs }}" class="px-3 py-2 bg-white border rounded">Export PDF</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="bg-white rounded shadow p-4">
    <h3 class="font-medium mb-2">Saldo per Akun</h3>
    <table class="min-w-full text-sm">
      <thead class="text-gray-600"><tr class="text-left"><th class="py-2 pr-4">Akun</th><th class="py-2 pr-4 text-right">Saldo</th></tr></thead>
      <tbody>
        @forelse($accountBalances as $ab)
        <tr class="border-t">
          <td class="py-2 pr-4">{{ $ab['account']->name }} ({{ $ab['account']->code }})</td>
          <td class="py-2 pr-4 text-right">Rp {{ number_format($ab['balance'],0,',','.') }}</td>
        </tr>
        @empty
        <tr><td colspan="2" class="py-6 text-center text-gray-500">Tidak ada data akun</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="bg-white rounded shadow p-4">
    <h3 class="font-medium mb-2">Saldo per Program</h3>
    <table class="min-w-full text-sm">
      <thead class="text-gray-600"><tr class="text-left"><th class="py-2 pr-4">Program</th><th class="py-2 pr-4 text-right">Saldo</th></tr></thead>
      <tbody>
        @forelse($programBalances as $pb)
        <tr class="border-t">
          <td class="py-2 pr-4">{{ $pb['program']->name }}</td>
          <td class="py-2 pr-4 text-right">Rp {{ number_format($pb['balance'],0,',','.') }}</td>
        </tr>
        @empty
        <tr><td colspan="2" class="py-6 text-center text-gray-500">Tidak ada data program</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
