@extends('layouts.app')
@section('content')
@include('partials.flash')

<div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">Penerimaan Dana</h2>
  <a href="{{ route('finance.incomes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">Catat Penerimaan</a>
</div>

<form method="GET" class="mb-4 flex gap-2 flex-wrap">
  <input name="search" value="{{ request('search') }}" placeholder="Cari no kwitansi / ref" class="border rounded px-3 py-2 w-64" />
  <select name="channel" class="border rounded px-3 py-2">
    <option value="">Semua Kanal</option>
    @foreach(['transfer','qris','va','tunai','gateway'] as $ch)
      <option value="{{ $ch }}" @selected(request('channel')===$ch)>{{ strtoupper($ch) }}</option>
    @endforeach
  </select>
  <button class="px-3 py-2 bg-gray-100 rounded">Filter</button>
  <a href="{{ route('finance.incomes.index') }}" class="px-3 py-2 text-sm underline">Reset</a>
</form>

<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="text-gray-600">
      <tr class="text-left">
        <th class="py-2 px-3">Tgl</th>
        <th class="py-2 px-3">No Kwitansi</th>
        <th class="py-2 px-3">Kanal</th>
        <th class="py-2 px-3">Donatur</th>
        <th class="py-2 px-3">Earmark</th>
        <th class="py-2 px-3">Jumlah</th>
        <th class="py-2 px-3">Status</th>
        <th class="py-2 px-3 w-48">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $r)
      <tr class="border-t">
        <td class="py-2 px-3">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
        <td class="py-2 px-3 font-mono">{{ $r->receipt_no }}</td>
        <td class="py-2 px-3 uppercase">{{ $r->channel }}</td>
        <td class="py-2 px-3">{{ $r->donor->name ?? '-' }}</td>
        <td class="py-2 px-3">{{ $r->program->name ?? 'General Fund' }}</td>
        <td class="py-2 px-3">Rp {{ number_format($r->amount,0,',','.') }}</td>
        <td class="py-2 px-3">{{ $r->status }}</td>
        <td class="py-2 px-3">
          <div class="flex gap-2">
            <a class="px-2 py-1 bg-white border rounded" href="{{ route('finance.incomes.show',$r) }}">Detail</a>
            <a class="px-2 py-1 bg-gray-100 rounded" href="{{ route('finance.incomes.edit',$r) }}">Ubah</a>
            <form method="POST" action="{{ route('finance.incomes.destroy',$r) }}" onsubmit="return confirm('Hapus data ini?')">
              @csrf @method('DELETE')
              <button class="px-2 py-1 bg-red-50 text-red-700 rounded">Hapus</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" class="py-6 text-center text-gray-500">Belum ada data</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $rows->links() }}</div>

<div class="mt-8 bg-white rounded shadow p-4">
  <div class="flex items-center justify-between mb-2">
    <h3 class="font-medium">Auto-Match Mutasi Bank</h3>
    <form method="POST" action="{{ route('finance.mutations.automatch') }}">@csrf <button class="px-3 py-1.5 bg-emerald-600 text-white rounded">Jalankan</button></form>
  </div>
  <form method="POST" action="{{ route('finance.mutations.import') }}" enctype="multipart/form-data" class="flex items-center gap-3 text-sm">
    @csrf
    <input type="file" name="file" accept=".csv,.txt" class="border rounded px-3 py-2" required />
    <button class="px-3 py-2 bg-gray-100 rounded">Import CSV Mutasi</button>
    <span class="text-gray-500">Format: tanggal(YYYY-MM-DD), amount, description, channel, ref_no</span>
  </form>
</div>
@endsection
