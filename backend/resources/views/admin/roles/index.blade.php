@extends('layouts.app')
@section('content')
@include('partials.flash')

<div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">Peran & Permissions</h2>
  <form method="POST" action="{{ route('admin.roles.permissions.store') }}" class="flex items-center gap-2 text-sm">
    @csrf
    <input name="name" placeholder="permission baru (mis. finance.manage)" class="border rounded px-3 py-2" />
    <button class="px-3 py-2 bg-gray-100 rounded">Tambah Permission</button>
  </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  @foreach($roles as $role)
  <div class="bg-white rounded shadow p-4">
    <div class="font-medium mb-2">Peran: {{ ucfirst($role->name) }}</div>
    <form method="POST" action="{{ route('admin.roles.permissions.update',$role) }}" class="text-sm">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-60 overflow-y-auto p-2 border rounded">
        @foreach($allPermissions as $p)
          <label class="flex items-center gap-2">
            <input type="checkbox" name="permissions[]" value="{{ $p->name }}" @checked($role->permissions->pluck('name')->contains($p->name)) />
            <span class="font-mono">{{ $p->name }}</span>
          </label>
        @endforeach
      </div>
      <div class="mt-3"><button class="px-3 py-2 bg-blue-600 text-white rounded">Simpan</button></div>
    </form>
  </div>
  @endforeach
</div>
@endsection

