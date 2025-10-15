@extends('layouts.app')
@section('content')
@include('partials.flash')

<div class="mb-4">
  <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline">← Kembali</a>
</div>

<h2 class="text-xl font-semibold mb-4">Atur Peran: {{ $user->name }}</h2>

<form method="POST" action="{{ route('admin.users.update',$user) }}" class="bg-white rounded shadow p-4 max-w-xl">
  @csrf
  @method('PUT')
  <div class="space-y-2">
    @foreach($roles as $r)
      <label class="flex items-center gap-2">
        <input type="checkbox" name="roles[]" value="{{ $r->name }}" @checked(in_array($r->name,$userRoles)) />
        <span>{{ ucfirst($r->name) }}</span>
      </label>
    @endforeach
  </div>
  <div class="mt-4 flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 rounded">Batal</a>
  </div>
</form>
@endsection

