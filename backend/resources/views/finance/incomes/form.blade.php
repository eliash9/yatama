@extends('layouts.app')
@section('content')
@include('partials.flash')

<h2 class="text-xl font-semibold mb-4">{{ $row->exists ? 'Ubah Penerimaan Dana' : 'Catat Penerimaan Dana' }}</h2>

<form method="POST" action="{{ $row->exists ? route('finance.incomes.update',$row) : route('finance.incomes.store') }}" enctype="multipart/form-data" class="bg-white rounded shadow p-4 max-w-3xl">
  @csrf
  @if($row->exists) @method('PUT') @endif
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium">Tanggal</label>
      <input type="date" name="tanggal" value="{{ old('tanggal',$row->tanggal) }}" required class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Kanal</label>
      <select name="channel" class="w-full border rounded px-3 py-2" required>
        @foreach(['transfer','qris','va','tunai','gateway'] as $ch)
          <option value="{{ $ch }}" @selected(old('channel',$row->channel)===$ch)>{{ strtoupper($ch) }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Jumlah (Rp)</label>
      <input type="number" name="amount" min="0" value="{{ old('amount',$row->amount) }}" required class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Donatur</label>
      <select name="donor_id" class="w-full border rounded px-3 py-2">
        <option value="">-</option>
        @foreach($donors as $d)
          <option value="{{ $d->id }}" @selected(old('donor_id',$row->donor_id)==$d->id)>{{ $d->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Earmark ke Program (opsional)</label>
      <select name="program_id" class="w-full border rounded px-3 py-2">
        <option value="">General Fund</option>
        @foreach($programs as $p)
          <option value="{{ $p->id }}" @selected(old('program_id',$row->program_id)==$p->id)>{{ $p->name }}</option>
        @endforeach
      </select>
      <p class="text-xs text-gray-500 mt-1">Pilih program jika dana ditujukan khusus (earmark). Kosongkan untuk masuk General Fund.</p>
    </div>
    <div>
      <label class="block text-sm font-medium">Ref/No Transaksi</label>
      <input name="ref_no" value="{{ old('ref_no',$row->ref_no) }}" class="w-full border rounded px-3 py-2" />
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Catatan</label>
      <textarea name="notes" class="w-full border rounded px-3 py-2">{{ old('notes',$row->notes) }}</textarea>
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Bukti Bayar (upload)</label>
      <input type="file" name="receipt" accept="image/*,application/pdf" class="w-full border rounded px-3 py-2" />
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
    <a href="{{ route('finance.incomes.index') }}" class="px-4 py-2 rounded bg-gray-100">Batal</a>
  </div>
</form>
@endsection

