@extends('layouts.app')
@section('content')
@include('partials.flash')

<h2 class="text-xl font-semibold mb-4">{{ $row->exists ? 'Ubah Penerima' : 'Tambah Penerima' }}</h2>

<form method="POST" action="{{ $row->exists ? route('master.beneficiaries.update',$row) : route('master.beneficiaries.store') }}" class="bg-white rounded shadow p-4 max-w-2xl">
  @csrf
  @if($row->exists) @method('PUT') @endif
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium">Kode</label>
      <input name="code" value="{{ old('code',$row->code) }}" required class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Tipe</label>
      <input name="type" value="{{ old('type',$row->type) }}" placeholder="anak/keluarga/panti" required class="w-full border rounded px-3 py-2" />
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Nama</label>
      <input name="name" value="{{ old('name',$row->name) }}" required class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Tanggal Lahir</label>
      <input name="date_of_birth" type="date" value="{{ old('date_of_birth',$row->date_of_birth) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Nama Wali</label>
      <input name="guardian_name" value="{{ old('guardian_name',$row->guardian_name) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">NIK</label>
      <input name="national_id" value="{{ old('national_id',$row->national_id) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">No. KK</label>
      <input name="family_card_no" value="{{ old('family_card_no',$row->family_card_no) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Jenis Kelamin</label>
      <select name="gender" class="w-full border rounded px-3 py-2">
        <option value="">-</option>
        <option value="male" @selected(old('gender',$row->gender)==='male')>Laki-laki</option>
        <option value="female" @selected(old('gender',$row->gender)==='female')>Perempuan</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Pendidikan</label>
      <input name="education" value="{{ old('education',$row->education) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Pekerjaan</label>
      <input name="occupation" value="{{ old('occupation',$row->occupation) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Email</label>
      <input name="email" type="email" value="{{ old('email',$row->email) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Telepon</label>
      <input name="phone" value="{{ old('phone',$row->phone) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Telepon Wali</label>
      <input name="guardian_phone" value="{{ old('guardian_phone',$row->guardian_phone) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Alamat</label>
      <input name="address" value="{{ old('address',$row->address) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Kota/Kabupaten</label>
      <input name="city" value="{{ old('city',$row->city) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Provinsi</label>
      <input name="province" value="{{ old('province',$row->province) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Kode Pos</label>
      <input name="postal_code" value="{{ old('postal_code',$row->postal_code) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Catatan</label>
      <textarea name="notes" class="w-full border rounded px-3 py-2">{{ old('notes',$row->notes) }}</textarea>
    </div>
    <div class="flex items-center gap-2 mt-6 md:col-span-2">
      <input type="checkbox" name="is_active" value="1" @checked(old('is_active',$row->is_active)) />
      <span>Aktif</span>
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
    <a href="{{ route('master.beneficiaries.index') }}" class="px-4 py-2 rounded bg-gray-100">Batal</a>
  </div>
</form>
@endsection
