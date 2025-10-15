@extends('layouts.app')
@section('content')
@include('partials.flash')

<div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">Master Program/Kampanye</h2>
  <a href="{{ route('master.programs.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">Tambah</a>
</div>

<form method="GET" class="mb-4 flex gap-2 flex-wrap">
  <input name="search" value="{{ request('search') }}" placeholder="Cari nama/kode/kategori" class="border rounded px-3 py-2 w-64" />
  <select name="type" class="border rounded px-3 py-2">
    <option value="">Semua Jenis</option>
    <option value="program" @selected(request('type')==='program')>Program</option>
    <option value="campaign" @selected(request('type')==='campaign')>Kampanye</option>
  </select>
  <input name="status" value="{{ request('status') }}" placeholder="Status (active/done)" class="border rounded px-3 py-2 w-48" />
  <button class="px-3 py-2 bg-gray-100 rounded">Filter</button>
  <a href="{{ route('master.programs.index') }}" class="px-3 py-2 text-sm underline">Reset</a>
</form>

<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="text-gray-600">
      <tr class="text-left">
        <th class="py-2 px-3">Kode</th>
        <th class="py-2 px-3">Nama</th>
        <th class="py-2 px-3">Kategori</th>
        <th class="py-2 px-3">Unit</th>
        <th class="py-2 px-3">Target</th>
        <th class="py-2 px-3">Status</th>
        <th class="py-2 px-3 w-40">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $r)
      <tr class="border-t">
        <td class="py-2 px-3 font-mono">{{ $r->code }}</td>
        <td class="py-2 px-3">{{ $r->name }}</td>
        <td class="py-2 px-3">{{ $r->category }}</td>
        <td class="py-2 px-3">{{ $r->unit->name ?? '-' }}</td>
        <td class="py-2 px-3">@if($r->target_amount) Rp {{ number_format($r->target_amount,0,',','.') }} @else - @endif</td>
        <td class="py-2 px-3">{{ $r->status }}</td>
        <td class="py-2 px-3">
          <div class="flex gap-2">
            <a class="px-2 py-1 bg-gray-100 rounded" href="{{ route('master.programs.edit',$r) }}">Ubah</a>
            <form method="POST" action="{{ route('master.programs.destroy',$r) }}" onsubmit="return confirm('Hapus data ini?')">
              @csrf @method('DELETE')
              <button class="px-2 py-1 bg-red-50 text-red-700 rounded">Hapus</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" class="py-6 text-center text-gray-500">Belum ada data</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $rows->links() }}</div>
@endsection

