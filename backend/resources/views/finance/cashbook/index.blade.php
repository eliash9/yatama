@extends('layouts.app')
@section('content')
<h2 class="text-xl font-semibold mb-4">Buku Kas</h2>

<form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-3 text-sm">
  <div>
    <label class="block text-gray-600 mb-1">Akun</label>
    <select name="account_id" class="w-full border rounded px-3 py-2">
      <option value="">Pilih Akun</option>
      @foreach($accounts as $a)
      <option value="{{ $a->id }}" @selected(request('account_id')==$a->id)>{{ $a->name }} ({{ $a->code }})</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="block text-gray-600 mb-1">Dari</label>
    <input type="date" name="from" value="{{ $from }}" class="w-full border rounded px-3 py-2" />
  </div>
  <div>
    <label class="block text-gray-600 mb-1">Sampai</label>
    <input type="date" name="to" value="{{ $to }}" class="w-full border rounded px-3 py-2" />
  </div>
  <div class="md:col-span-2 flex items-end gap-2">
    <button class="px-3 py-2 bg-gray-100 rounded">Terapkan</button>
    <a href="{{ route('finance.cashbook') }}" class="px-3 py-2 underline">Reset</a>
  </div>
</form>

@if($account)
<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="text-gray-600">
      <tr class="text-left">
        <th class="py-2 px-3">Tanggal</th>
        <th class="py-2 px-3">Ref</th>
        <th class="py-2 px-3">Memo</th>
        <th class="py-2 px-3">Debit</th>
        <th class="py-2 px-3">Kredit</th>
        <th class="py-2 px-3">Saldo Berjalan</th>
      </tr>
    </thead>
    <tbody>
      <tr class="border-t bg-gray-50">
        <td class="py-2 px-3" colspan="5">Saldo Awal</td>
        <td class="py-2 px-3 font-medium">Rp {{ number_format($opening,0,',','.') }}</td>
      </tr>
      @php $running = $opening; @endphp
      @forelse($rows as $r)
        @php $debit = $r->jenis==='debit' ? (int)$r->amount : 0; $kredit = $r->jenis==='kredit' ? (int)$r->amount : 0; $running += $debit - $kredit; @endphp
        <tr class="border-t">
          <td class="py-2 px-3">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
          <td class="py-2 px-3 font-mono">#{{ $r->id }}</td>
          <td class="py-2 px-3">{{ $r->memo }}</td>
          <td class="py-2 px-3">@if($debit) Rp {{ number_format($debit,0,',','.') }} @endif</td>
          <td class="py-2 px-3">@if($kredit) Rp {{ number_format($kredit,0,',','.') }} @endif</td>
          <td class="py-2 px-3">Rp {{ number_format($running,0,',','.') }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="py-6 text-center text-gray-500">Tidak ada transaksi pada rentang ini</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endif
@endsection

