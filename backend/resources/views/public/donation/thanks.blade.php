@extends('layouts.public')
@section('content')
  <div class="text-center mb-4">
    <h1 class="text-2xl font-semibold">Terima Kasih!</h1>
    <p class="text-sm text-gray-500">Donasi Anda telah tercatat.</p>
  </div>

  <div class="bg-white rounded-xl shadow p-4 text-sm">
    <div class="flex justify-between"><span>No Kwitansi</span><span class="font-mono">{{ $row->receipt_no }}</span></div>
    <div class="flex justify-between"><span>Tanggal</span><span>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</span></div>
    <div class="flex justify-between"><span>Jumlah</span><span class="font-medium">Rp {{ number_format($row->amount,0,',','.') }}</span></div>
    <div class="flex justify-between"><span>Kanal</span><span class="uppercase">{{ $row->channel }}</span></div>
  </div>

  <div class="mt-4 text-center">
    <a href="{{ route('public.donation.index') }}" class="inline-block bg-blue-600 text-white rounded-lg px-4 py-2">Donasi Lagi</a>
  </div>
@endsection
