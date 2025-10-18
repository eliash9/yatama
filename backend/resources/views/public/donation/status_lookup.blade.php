@extends('layouts.public')
@section('content')
  <div class="text-center mb-4">
    <h1 class="text-2xl font-semibold">Cek Status Donasi</h1>
    <p class="text-sm text-gray-500">Masukkan kode referensi (ref)</p>
  </div>

  <form method="GET" action="{{ route('public.donation.status') }}" class="bg-white rounded-xl shadow p-4 space-y-3">
    <div>
      <label class="block text-sm text-gray-600 mb-1">Ref</label>
      <input name="ref" required class="w-full border rounded-lg px-4 py-3" placeholder="Tempel kode referensi" />
    </div>
    <button class="w-full bg-blue-600 text-white rounded-lg py-3">Cek Status</button>
  </form>
@endsection

