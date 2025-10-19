@extends('layouts.public')
@section('content')
<h1 class="text-xl font-semibold mb-4">Masuk Donatur</h1>
@if($errors->any())
  <div class="mb-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded px-3 py-2">{{ $errors->first() }}</div>
@endif
@if(session('status'))
  <div class="mb-3 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded px-3 py-2">{{ session('status') }}</div>
@endif

<div class="space-y-4">
  <a href="{{ route('public.donation.account.google.redirect') }}" class="block w-full text-center bg-white border rounded-lg py-3 hover:bg-gray-50">Lanjut dengan Google</a>

  <form method="POST" action="{{ route('public.donation.account.request') }}" class="bg-white rounded-xl shadow p-4 space-y-3">
    @csrf
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
    <button class="w-full bg-blue-600 text-white rounded-lg py-3">Kirim Kode Login</button>
    <div class="text-xs text-gray-500">Kode berlaku 15 menit. Saat dev, cek log aplikasi.</div>
  </form>
</div>

<div class="mt-4 text-center">
  <a href="{{ route('public.donation.account.verify') }}" class="text-blue-600 underline">Sudah punya kode? Verifikasi</a>
  <div class="mt-2"><a href="{{ route('public.donation.index') }}" class="text-gray-600 underline">Kembali</a></div>
</div>
@endsection



