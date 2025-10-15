@extends('layouts.app')
@section('content')
<h2 class="text-xl font-semibold mb-4">Saldo Akun & Program</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="bg-white rounded shadow p-4">
    <h3 class="font-medium mb-2">Saldo per Akun</h3>
    <table class="min-w-full text-sm">
      <thead class="text-gray-600"><tr class="text-left"><th class="py-2 pr-4">Akun</th><th class="py-2 pr-4">Saldo</th></tr></thead>
      <tbody>
        @forelse($accountBalances as $ab)
        <tr class="border-t">
          <td class="py-2 pr-4">{{ $ab['account']->name }} ({{ $ab['account']->code }})</td>
          <td class="py-2 pr-4">Rp {{ number_format($ab['balance'],0,',','.') }}</td>
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
      <thead class="text-gray-600"><tr class="text-left"><th class="py-2 pr-4">Program</th><th class="py-2 pr-4">Saldo</th></tr></thead>
      <tbody>
        @forelse($programBalances as $pb)
        <tr class="border-t">
          <td class="py-2 pr-4">{{ $pb['program']->name }}</td>
          <td class="py-2 pr-4">Rp {{ number_format($pb['balance'],0,',','.') }}</td>
        </tr>
        @empty
        <tr><td colspan="2" class="py-6 text-center text-gray-500">Tidak ada data program</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

