@extends('layouts.app')
@section('content')
@include('partials.flash')

<div class="mb-4">
  <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline">← Kembali</a>
  </div>

<h2 class="text-xl font-semibold mb-4">Tambah Pengguna</h2>

<form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded shadow p-4 max-w-xl">
  @csrf
  <div class="space-y-3">
    <div>
      <label class="block text-sm mb-1">Nama</label>
      <input name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2" />
      @error('name') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
    </div>
    <div>
      <label class="block text-sm mb-1">Email</label>
      <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded px-3 py-2" />
      @error('email') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div>
        <label class="block text-sm mb-1">Password</label>
        <input type="password" name="password" required class="w-full border rounded px-3 py-2" />
        @error('password') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
      </div>
      <div>
        <label class="block text-sm mb-1">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" required class="w-full border rounded px-3 py-2" />
      </div>
    </div>
    <div>
      <label class="block text-sm mb-1">Peran</label>
      <div class="space-y-1">
        @foreach($roles as $r)
          <label class="inline-flex items-center gap-2 mr-4">
            <input type="checkbox" name="roles[]" value="{{ $r->name }}" />
            <span>{{ ucfirst($r->name) }}</span>
          </label>
        @endforeach
      </div>
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 rounded">Batal</a>
  </div>
</form>
@endsection

