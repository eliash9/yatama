@extends('layouts.app')
@section('content')
@include('partials.flash')

<h2 class="text-xl font-semibold mb-4">{{ $row->exists ? 'Ubah Transaksi' : 'Catat Transaksi' }}</h2>

<form method="POST" action="{{ $row->exists ? route('finance.transactions.update',$row) : route('finance.transactions.store') }}" class="bg-white rounded shadow p-4 max-w-2xl">
  @csrf
  @if($row->exists) @method('PUT') @endif
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium">Tanggal</label>
      <input type="date" name="tanggal" value="{{ old('tanggal',$row->tanggal) }}" required class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Jenis</label>
      <select name="jenis" class="w-full border rounded px-3 py-2">
        <option value="debit" @selected(old('jenis',$row->jenis)==='debit')>Debit</option>
        <option value="kredit" @selected(old('jenis',$row->jenis)==='kredit')>Kredit</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Akun Kas/Bank</label>
      <select name="account_id" class="w-full border rounded px-3 py-2" required>
        <option value="">Pilih Akun</option>
        @foreach(($accounts ?? []) as $a)
          <option value="{{ $a->id }}" @selected(old('account_id',$row->account_id)==$a->id)>{{ $a->name }} ({{ $a->code }})</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Jumlah (Rp)</label>
      <input type="number" min="0" name="amount" value="{{ old('amount',$row->amount) }}" required class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Program (opsional)</label>
      <select name="program_id" class="w-full border rounded px-3 py-2">
        <option value="">-</option>
        @foreach(($programs ?? []) as $p)
          <option value="{{ $p->id }}" @selected(old('program_id',$row->program_id)==$p->id)>{{ $p->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium">Memo</label>
      <input name="memo" value="{{ old('memo',$row->memo) }}" class="w-full border rounded px-3 py-2" />
    </div>
  </div>
  <div class="mt-4 flex gap-2">
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
    <a href="{{ route('finance.transactions.index') }}" class="px-4 py-2 rounded bg-gray-100">Batal</a>
  </div>
</form>
@endsection
