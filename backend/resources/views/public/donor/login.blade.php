@extends('layouts.public')
@section('content')
<h1 class="text-xl font-semibold mb-4">Masuk Donatur</h1>
@if(session('status'))
  <div class="mb-3 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded px-3 py-2">{{ session('status') }}</div>
@endif
<form method="POST" action="{{ route('public.donor.request') }}" class="bg-white rounded-xl shadow p-4 space-y-3">
  @csrf
  <div>
    <label class="block text-sm text-gray-600 mb-1">Email</label>
    <input type="email" name="email" required class="w-full border rounded-lg px-4 py-3" placeholder="email@anda.com" />
  </div>
  <button class="w-full bg-blue-600 text-white rounded-lg py-3">Kirim Kode Login</button>
  <div class="text-xs text-gray-500">Kode akan berlaku 15 menit. Saat dev, cek log aplikasi.</div>
</form>
<div class="mt-4 text-center">
  <a href="{{ route('public.donor.verify') }}" class="text-blue-600 underline">Sudah punya kode? Verifikasi</a>
  <div class="mt-2"><a href="{{ route('public.donation.index') }}" class="text-gray-600 underline">Kembali</a></div>
</div>
@endsection

