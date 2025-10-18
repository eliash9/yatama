@extends('layouts.public')
@section('content')
  <div class="text-center mb-4">
    <h1 class="text-2xl font-semibold">Status Pembayaran</h1>
    <p class="text-sm text-gray-500">Ref: <span class="font-mono">{{ $row->ref_no }}</span></p>
  </div>

  <div class="bg-white rounded-xl shadow p-4 space-y-2 text-sm">
    <div class="flex justify-between"><span>Jumlah</span><span class="font-medium">Rp {{ number_format($row->amount,0,',','.') }}</span></div>
    <div class="flex justify-between"><span>Kanal</span><span class="uppercase">{{ $row->channel }}</span></div>
    <div class="flex justify-between"><span>Status</span><span class="capitalize">{{ $row->status }}</span></div>
    <div class="flex justify-between"><span>Tanggal</span><span>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</span></div>
  </div>

  @if($row->status==='recorded')
  <div class="mt-4 bg-white rounded-xl shadow p-4">
    @if($row->channel==='transfer')
      <h3 class="font-medium mb-2">Instruksi Transfer</h3>
      <ol class="list-decimal ml-5 text-sm space-y-1">
        <li>Transfer sesuai nominal ke rekening yayasan.</li>
        <li>Tambahkan berita: <span class="font-mono">{{ $row->ref_no }}</span></li>
        <li>Setelah transfer, sistem akan memadankan otomatis.</li>
      </ol>
    @elseif($row->channel==='qris')
      <h3 class="font-medium mb-2">Scan QRIS</h3>
      @if($qrisUrl)
        <img src="{{ $qrisUrl }}" alt="QRIS" class="w-64 h-64 mx-auto rounded border" />
      @else
        <p class="text-sm text-amber-600">QRIS belum dikonfigurasi. Hubungi admin.</p>
      @endif
      <p class="text-xs text-gray-500 mt-2">Gunakan berita: <span class="font-mono">{{ $row->ref_no }}</span></p>
    @endif
  </div>
  @endif

  <div class="mt-4 text-center">
    @if($row->status==='matched')
      <a href="{{ route('public.donation.thanks',['ref'=>$row->ref_no]) }}" class="inline-block bg-emerald-600 text-white rounded-lg px-4 py-2">Lanjut ke Terima Kasih</a>
    @else
      <a href="{{ route('public.donation.status',['ref'=>$row->ref_no]) }}" class="inline-block text-blue-600 underline">Refresh Status</a>
    @endif
  </div>
@endsection

