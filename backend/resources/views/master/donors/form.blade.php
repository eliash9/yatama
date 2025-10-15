@extends('layouts.app')
@section('content')
@include('partials.flash')

<h2 class="text-xl font-semibold mb-4">{{ $row->exists ? 'Ubah Donatur' : 'Tambah Donatur' }}</h2>

<form method="POST" action="{{ $row->exists ? route('master.donors.update',$row) : route('master.donors.store') }}" class="bg-white rounded shadow p-4 max-w-2xl">
  @csrf
  @if($row->exists) @method('PUT') @endif
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium">Kode</label>
      <input name="code" value="{{ old('code',$row->code) }}" required class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Tipe</label>
      <select name="type" class="w-full border rounded px-3 py-2" required>
        <option value="individual" @selected(old('type',$row->type)=='individual')>Perorangan</option>
        <option value="company" @selected(old('type',$row->type)=='company')>Perusahaan</option>
      </select>
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Nama</label>
      <input name="name" value="{{ old('name',$row->name) }}" required class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Email</label>
      <input name="email" type="email" value="{{ old('email',$row->email) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Telepon</label>
      <input name="phone" value="{{ old('phone',$row->phone) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Alamat</label>
      <input name="address" value="{{ old('address',$row->address) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">NPWP/Tax ID</label>
      <input name="tax_id" value="{{ old('tax_id',$row->tax_id) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div class="flex items-center gap-2 mt-6">
      <input type="checkbox" name="is_active" value="1" @checked(old('is_active',$row->is_active)) />
      <span>Aktif</span>
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
    <a href="{{ route('master.donors.index') }}" class="px-4 py-2 rounded bg-gray-100">Batal</a>
  </div>
</form>
@endsection

