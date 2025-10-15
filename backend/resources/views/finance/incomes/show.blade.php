@extends('layouts.app')
@section('content')
<div class="mb-4">
  <a href="{{ route('finance.incomes.index') }}" class="text-sm text-gray-600 hover:underline">← Kembali ke daftar</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  <div class="md:col-span-2">
    <div class="bg-white rounded shadow p-4">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-semibold">Detail Penerimaan</h2>
        <div class="flex items-center gap-2">
          <a href="{{ route('finance.incomes.receipt',$row) }}" target="_blank" class="px-3 py-1.5 bg-white border rounded">E‑Receipt</a>
          <a href="{{ route('finance.incomes.edit',$row) }}" class="px-3 py-1.5 bg-gray-100 rounded">Ubah</a>
        </div>
      </div>
      <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-gray-500">No Kwitansi</dt><dd class="font-mono">{{ $row->receipt_no }}</dd></div>
        <div><dt class="text-gray-500">Tanggal</dt><dd>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</dd></div>
        <div><dt class="text-gray-500">Kanal</dt><dd class="uppercase">{{ $row->channel }}</dd></div>
        <div><dt class="text-gray-500">Jumlah</dt><dd>Rp {{ number_format($row->amount,0,',','.') }}</dd></div>
        <div><dt class="text-gray-500">Donatur</dt><dd>{{ $row->donor->name ?? '-' }}</dd></div>
        <div><dt class="text-gray-500">Earmark</dt><dd>{{ $row->program->name ?? 'General Fund' }}</dd></div>
        <div><dt class="text-gray-500">Ref/No Transaksi</dt><dd>{{ $row->ref_no ?? '-' }}</dd></div>
        <div><dt class="text-gray-500">Status</dt><dd>{{ $row->status }}</dd></div>
        <div class="md:col-span-2"><dt class="text-gray-500">Catatan</dt><dd>{{ $row->notes ?? '-' }}</dd></div>
      </dl>
    </div>
  </div>
  <div>
    <div class="bg-white rounded shadow p-4">
      <h3 class="font-medium mb-2">Bukti Bayar</h3>
      @if($attachments->isEmpty())
        <p class="text-sm text-gray-500">Belum ada bukti terunggah.</p>
      @else
        <ul class="text-sm space-y-2">
          @foreach($attachments as $a)
            <li class="flex items-center justify-between">
              <a class="text-blue-600 hover:underline" href="/{{ $a->url }}" target="_blank">{{ $a->filename }}</a>
              <span class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($a->uploaded_at)->format('d M Y H:i') }}</span>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
</div>
@endsection
