@extends('layouts.app')
@section('content')
@include('partials.flash')

<h2 class="text-xl font-semibold mb-4">{{ $row->exists ? 'Ubah Akun' : 'Tambah Akun' }}</h2>

<form method="POST" action="{{ $row->exists ? route('master.accounts.update',$row) : route('master.accounts.store') }}" class="bg-white rounded shadow p-4 max-w-2xl">
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
      <label class="block text-sm font-medium">Tipe</label>
      <select name="type" class="w-full border rounded px-3 py-2" required>
        <option value="cash" @selected(old('type',$row->type)=='cash')>Kas</option>
        <option value="bank" @selected(old('type',$row->type)=='bank')>Bank</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Bank</label>
      <input name="bank_name" value="{{ old('bank_name',$row->bank_name) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">No Rekening</label>
      <input name="account_no" value="{{ old('account_no',$row->account_no) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Saldo Awal (Rp)</label>
      <input type="number" min="0" name="opening_balance" value="{{ old('opening_balance',$row->opening_balance) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div class="flex items-center gap-2 mt-6">
      <input type="checkbox" name="is_active" value="1" @checked(old('is_active',$row->is_active)) />
      <span>Aktif</span>
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
    <a href="{{ route('master.accounts.index') }}" class="px-4 py-2 rounded bg-gray-100">Batal</a>
  </div>
</form>
@endsection

