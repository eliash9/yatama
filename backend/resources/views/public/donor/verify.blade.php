@extends('layouts.public')
@section('content')
<h1 class="text-xl font-semibold mb-4">Verifikasi Kode</h1>
@if($errors->any())
  <div class="mb-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded px-3 py-2">{{ $errors->first() }}</div>
@endif
@if(session('status'))
  <div class="mb-3 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded px-3 py-2">{{ session('status') }}</div>
@endif
<form method="POST" action="{{ route('public.donation.account.verify.post') }}" class="bg-white rounded-xl shadow p-4 space-y-3">
  @csrf
  @if(!empty($prefillValue))
    <div>
      <label class="block text-sm text-gray-600 mb-1">{{ $prefillType === 'phone' ? 'Nomor HP' : 'Email' }}</label>
      <div class="w-full border rounded-lg px-4 py-3 bg-gray-50">{{ $prefillValue }}</div>
      <input type="hidden" name="{{ $prefillType }}" value="{{ $prefillValue }}" />
      <div class="text-xs text-gray-500 mt-1">Data diambil dari sesi sebelumnya</div>
    </div>
  @else
    <div>
      <label class="block text-sm text-gray-600 mb-1">Email</label>
      <input type="email" name="email" class="w-full border rounded-lg px-4 py-3" placeholder="email@anda.com" />
    </div>
    <div>
      <div class="text-center text-xs text-gray-400">atau</div>
    </div>
    <div>
      <label class="block text-sm text-gray-600 mb-1">Nomor HP</label>
      <input type="text" name="phone" class="w-full border rounded-lg px-4 py-3" placeholder="08xxx atau +62" />
    </div>
  @endif
  <div>
    <label class="block text-sm text-gray-600 mb-1">Kode</label>
    <input name="token" required class="w-full border rounded-lg px-4 py-3" placeholder="XXXXXX" />
  </div>
  <button class="w-full bg-blue-600 text-white rounded-lg py-3">Masuk</button>
</form>
<div class="mt-4 text-center">
  <a href="{{ route('public.donation.account.login') }}" class="text-gray-600 underline">Kembali</a>
</div>
@endsection




