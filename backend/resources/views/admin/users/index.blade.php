@extends('layouts.app')
@section('content')
@include('partials.flash')

<div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">Pengguna</h2>
</div>

<form method="GET" class="mb-4 flex gap-2">
  <input name="search" value="{{ request('search') }}" placeholder="Cari nama/email" class="border rounded px-3 py-2 w-64" />
  <button class="px-3 py-2 bg-gray-100 rounded">Filter</button>
  <a href="{{ route('admin.users.index') }}" class="px-3 py-2 underline">Reset</a>
</form>

<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="text-gray-600"><tr class="text-left"><th class="py-2 px-3">Nama</th><th class="py-2 px-3">Email</th><th class="py-2 px-3">Peran</th><th class="py-2 px-3 w-40">Aksi</th></tr></thead>
    <tbody>
      @forelse($users as $u)
      <tr class="border-t">
        <td class="py-2 px-3">{{ $u->name }}</td>
        <td class="py-2 px-3">{{ $u->email }}</td>
        <td class="py-2 px-3">{{ $u->roles->pluck('name')->implode(', ') ?: '-' }}</td>
        <td class="py-2 px-3"><a class="px-2 py-1 bg-gray-100 rounded" href="{{ route('admin.users.edit',$u) }}">Atur Peran</a></td>
      </tr>
      @empty
      <tr><td colspan="4" class="py-6 text-center text-gray-500">Tidak ada pengguna</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection

