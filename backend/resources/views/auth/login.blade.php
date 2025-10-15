@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
  <div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold mb-4">Masuk</h2>

    @if ($errors->any())
      <div class="mb-4 bg-red-50 text-red-700 border border-red-200 rounded p-3 text-sm">
        <ul class="list-disc pl-5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ url('/login') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full border rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input name="password" type="password" required class="w-full border rounded px-3 py-2" />
      </div>
      <div class="flex items-center justify-between">
        <label class="inline-flex items-center text-sm"><input type="checkbox" name="remember" class="mr-2"> Ingat saya</label>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Masuk</button>
      </div>
    </form>
  </div>

  <p class="text-xs text-gray-500 mt-4">Gunakan akun admin bawaan: admin@example.com / password (ubah di produksi).</p>
</div>
@endsection

