@extends('layouts.app')
@section('content')
@include('partials.flash')

<div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">Master Penerima Manfaat</h2>
  <a href="{{ route('master.beneficiaries.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">Tambah</a>
</div>

<form method="GET" class="mb-4 flex gap-2">
  <input name="search" value="{{ request('search') }}" placeholder="Cari nama/kode/wali" class="border rounded px-3 py-2 w-64" />
  <input name="type" value="{{ request('type') }}" placeholder="Tipe (anak/keluarga/panti)" class="border rounded px-3 py-2 w-56" />
  <button class="px-3 py-2 bg-gray-100 rounded">Filter</button>
  <a href="{{ route('master.beneficiaries.index') }}" class="px-3 py-2 text-sm underline">Reset</a>
</form>

<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="text-gray-600">
      <tr class="text-left">
        <th class="py-2 px-3">Kode</th>
        <th class="py-2 px-3">Nama</th>
        <th class="py-2 px-3">Tipe</th>
        <th class="py-2 px-3">Kontak</th>
        <th class="py-2 px-3">Status</th>
        <th class="py-2 px-3 w-40">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $r)
      <tr class="border-t">
        <td class="py-2 px-3 font-mono">{{ $r->code }}</td>
        <td class="py-2 px-3">{{ $r->name }}</td>
        <td class="py-2 px-3">{{ $r->type }}</td>
        <td class="py-2 px-3 text-gray-600">{{ $r->email }}<br>{{ $r->phone }}</td>
        <td class="py-2 px-3">{!! $r->is_active ? '<span class="text-green-600">Aktif</span>' : '<span class="text-gray-500">Nonaktif</span>' !!}</td>
        <td class="py-2 px-3">
          <div class="flex gap-2">
            <a class="px-2 py-1 bg-gray-100 rounded" href="{{ route('master.beneficiaries.edit',$r) }}">Ubah</a>
            <form method="POST" action="{{ route('master.beneficiaries.destroy',$r) }}" onsubmit="return confirm('Hapus data ini?')">
              @csrf @method('DELETE')
              <button class="px-2 py-1 bg-red-50 text-red-700 rounded">Hapus</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" class="py-6 text-center text-gray-500">Belum ada data</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $rows->links() }}</div>
@endsection

