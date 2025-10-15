@extends('layouts.app')
@section('content')
@include('partials.flash')

<h2 class="text-xl font-semibold mb-4">{{ $row->exists ? 'Ubah Pengajuan' : 'Buat Pengajuan Penyaluran' }}</h2>

<form method="POST" action="{{ $row->exists ? route('finance.disbursements.update',$row) : route('finance.disbursements.store') }}" class="bg-white rounded shadow p-4 max-w-3xl">
  @csrf
  @if($row->exists) @method('PUT') @endif
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium">Program</label>
      <select name="program_id" class="w-full border rounded px-3 py-2" required>
        @foreach($programs as $p)
          <option value="{{ $p->id }}" @selected(old('program_id',$row->program_id)==$p->id)>{{ $p->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Penerima Manfaat</label>
      <select name="beneficiary_id" class="w-full border rounded px-3 py-2" required>
        @foreach($beneficiaries as $b)
          <option value="{{ $b->id }}" @selected(old('beneficiary_id',$row->beneficiary_id)==$b->id)>{{ $b->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Jumlah (Rp)</label>
      <input type="number" min="1" name="amount" value="{{ old('amount',$row->amount) }}" class="w-full border rounded px-3 py-2" required />
    </div>
    <div>
      <label class="block text-sm font-medium">Preferensi Metode</label>
      <select name="method_preference" class="w-full border rounded px-3 py-2">
        <option value="">-</option>
        @foreach(['cash'=>'Tunai','transfer'=>'Transfer','ewallet'=>'E-Wallet'] as $k=>$v)
          <option value="{{ $k }}" @selected(old('method_preference',$row->method_preference)===$k)>{{ $v }}</option>
        @endforeach
      </select>
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Tujuan/Deskripsi</label>
      <textarea name="purpose" class="w-full border rounded px-3 py-2">{{ old('purpose',$row->purpose) }}</textarea>
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
    <a href="{{ route('finance.disbursements.index') }}" class="px-4 py-2 rounded bg-gray-100">Batal</a>
  </div>
</form>
@endsection

