@extends('layouts.public')
@section('content')
<h1 class="text-xl font-semibold mb-4">Verifikasi Kode</h1>
@if($errors->any())
  <div class="mb-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded px-3 py-2">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ route('public.donor.verify.post') }}" class="bg-white rounded-xl shadow p-4 space-y-3">
  @csrf
  <div>
    <label class="block text-sm text-gray-600 mb-1">Email</label>
    <input type="email" name="email" required class="w-full border rounded-lg px-4 py-3" placeholder="email@anda.com" />
  </div>
  <div>
    <label class="block text-sm text-gray-600 mb-1">Kode</label>
    <input name="token" required class="w-full border rounded-lg px-4 py-3" placeholder="XXXXXX" />
  </div>
  <button class="w-full bg-blue-600 text-white rounded-lg py-3">Masuk</button>
</form>
<div class="mt-4 text-center">
  <a href="{{ route('public.donor.login') }}" class="text-gray-600 underline">Kembali</a>
</div>
@endsection

