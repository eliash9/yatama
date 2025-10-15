@extends('layouts.app')
@section('content')
@include('partials.flash')

<div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">Mutasi Bank</h2>
  <div class="flex items-center gap-2">
    <form method="POST" action="{{ route('finance.mutations.automatch') }}">@csrf <button class="px-3 py-2 bg-emerald-600 text-white rounded">Auto-Match</button></form>
  </div>
</div>

<form method="POST" action="{{ route('finance.mutations.import') }}" enctype="multipart/form-data" class="mb-4 flex items-center gap-3 text-sm">
  @csrf
  <input type="file" name="file" accept=".csv,.txt" class="border rounded px-3 py-2" required />
  <button class="px-3 py-2 bg-gray-100 rounded">Import CSV Mutasi</button>
  <span class="text-gray-500">Format: tanggal(YYYY-MM-DD), amount, description, channel, ref_no</span>
  <a href="{{ route('finance.incomes.index') }}" class="ml-auto text-blue-600 hover:underline">› Penerimaan Dana</a>
</form>

<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="text-gray-600">
      <tr class="text-left">
        <th class="py-2 px-3">Tgl</th>
        <th class="py-2 px-3">Keterangan</th>
        <th class="py-2 px-3">Kanal</th>
        <th class="py-2 px-3">Ref</th>
        <th class="py-2 px-3">Jumlah</th>
        <th class="py-2 px-3">Income</th>
        <th class="py-2 px-3">Transaksi</th>
        <th class="py-2 px-3 w-[28rem]">Manual Match</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $m)
      <tr class="border-t">
        <td class="py-2 px-3">{{ \Carbon\Carbon::parse($m->tanggal)->format('d M Y') }}</td>
        <td class="py-2 px-3">{{ $m->description }}</td>
        <td class="py-2 px-3 uppercase">{{ $m->channel }}</td>
        <td class="py-2 px-3">{{ $m->ref_no }}</td>
        <td class="py-2 px-3">Rp {{ number_format($m->amount,0,',','.') }}</td>
        <td class="py-2 px-3">
          @if($m->income)
            <span class="font-mono">{{ $m->income->receipt_no }}</span>
          @else
            <span class="text-gray-500">-</span>
          @endif
        </td>
        <td class="py-2 px-3">
          @if($m->matched_transaction_id)
            <span class="font-mono">#{{ $m->matched_transaction_id }}</span>
          @else
            <span class="text-gray-500">-</span>
          @endif
        </td>
        <td class="py-2 px-3">
          @if(!$m->income)
          <form method="POST" action="{{ route('finance.mutations.match',$m) }}" class="flex items-center gap-2">
            @csrf
            <select name="income_id" class="border rounded px-2 py-1 text-sm">
              <option value="">Pilih income (terbaru)</option>
              @foreach($incomeCandidates as $c)
                <option value="{{ $c->id }}">{{ $c->receipt_no }} - Rp {{ number_format($c->amount,0,',','.') }} ({{ $c->tanggal }})</option>
              @endforeach
            </select>
            <span class="text-xs text-gray-400">atau</span>
            <input name="receipt_no" placeholder="Ketik No Kwitansi" class="border rounded px-2 py-1 text-sm w-40" />
            <button class="px-2 py-1 bg-blue-600 text-white rounded">Hubungkan</button>
          </form>
          @endif
          <div class="mt-2">
            @if(!$m->matched_transaction_id)
            <form method="POST" action="{{ route('finance.mutations.match',$m) }}" class="flex items-center gap-2">
              @csrf
              <select name="transaction_id" class="border rounded px-2 py-1 text-sm">
                <option value="">Pilih transaksi (terbaru)</option>
                @foreach($trxCandidates as $t)
                  <option value="{{ $t->id }}">#{{ $t->id }} - {{ $t->tanggal }} - Rp {{ number_format($t->amount,0,',','.') }} ({{ $t->jenis }})</option>
                @endforeach
              </select>
              <button class="px-2 py-1 bg-emerald-600 text-white rounded">Hubungkan ke Transaksi</button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" class="py-6 text-center text-gray-500">Belum ada data</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $rows->links() }}</div>
@endsection
