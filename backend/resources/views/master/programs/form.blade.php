@extends('layouts.app')
@section('content')
@include('partials.flash')

<h2 class="text-xl font-semibold mb-4">{{ $row->exists ? 'Ubah Program/Kampanye' : 'Tambah Program/Kampanye' }}</h2>

<form method="POST" enctype="multipart/form-data" action="{{ $row->exists ? route('master.programs.update',$row) : route('master.programs.store') }}" class="bg-white rounded shadow p-4 max-w-3xl">
  @csrf
  @if($row->exists) @method('PUT') @endif
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium">Kode</label>
      <input name="code" value="{{ old('code',$row->code) }}" required class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Nama</label>
      <input name="name" value="{{ old('name',$row->name) }}" required class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Kategori</label>
      <input name="category" value="{{ old('category',$row->category) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Jenis</label>
      <select name="type" class="w-full border rounded px-3 py-2">
        <option value="program" @selected(old('type',$row->type)=='program')>Program</option>
        <option value="campaign" @selected(old('type',$row->type)=='campaign')>Kampanye</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Unit</label>
      <select name="unit_id" class="w-full border rounded px-3 py-2">
        <option value="">-</option>
        @foreach($units as $u)
          <option value="{{ $u->id }}" @selected(old('unit_id',$row->unit_id)==$u->id)>{{ $u->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Target (Rp)</label>
      <input name="target_amount" type="number" min="0" value="{{ old('target_amount',$row->target_amount) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Mulai</label>
      <input name="start_date" type="date" value="{{ old('start_date',$row->start_date) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Selesai</label>
      <input name="end_date" type="date" value="{{ old('end_date',$row->end_date) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Deskripsi</label>
      <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description',$row->description) }}</textarea>
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Banner (opsional)</label>
      <input type="file" name="banner" accept="image/*" class="w-full border rounded px-3 py-2" />
      @if($row->banner_url)
        <div class="mt-2"><img src="/{{ $row->banner_url }}" alt="banner" class="h-24 rounded border" /></div>
      @endif
    </div>
    <div>
      <label class="block text-sm font-medium">Status</label>
      <input name="status" value="{{ old('status',$row->status) }}" placeholder="active/done" class="w-full border rounded px-3 py-2" />
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
    <a href="{{ route('master.programs.index') }}" class="px-4 py-2 rounded bg-gray-100">Batal</a>
  </div>
</form>
@endsection
