@extends('layouts.public')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-xl font-semibold">Donasi Saya</h1>
  <a href="{{ route('public.donor.dashboard') }}" class="text-sm text-gray-600 underline">Dashboard</a>
</div>

@if(session('status'))
  <div class="mb-3 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded px-3 py-2">{{ session('status') }}</div>
@endif
@if($errors->any())
  <div class="mb-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded px-3 py-2">{{ $errors->first() }}</div>
@endif

<div class="bg-white rounded-xl shadow p-4 mb-4">
  <div class="font-medium mb-2">Tautkan Donasi (Kwitansi)</div>
  <form method="POST" action="{{ route('public.donor.donations.claim') }}" class="flex gap-2 text-sm">
    @csrf
    <input name="receipt_no" class="flex-1 border rounded px-3 py-2" placeholder="Masukkan No Kwitansi (contoh: KW-20251018-ABC123)" required />
    <button class="bg-blue-600 text-white rounded px-3">Tautkan</button>
  </form>
  <div class="text-xs text-gray-500 mt-2">Gunakan fitur ini jika donasi Anda belum muncul di daftar.</div>
  </div>

<div class="bg-white rounded-xl shadow overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="text-gray-500"><tr class="text-left"><th class="py-2 px-3">Tanggal</th><th class="py-2 px-3">No Kwitansi</th><th class="py-2 px-3">Kanal</th><th class="py-2 px-3">Earmark</th><th class="py-2 px-3 text-right">Jumlah</th><th class="py-2 px-3">Status</th></tr></thead>
    <tbody>
      @forelse($rows as $r)
      <tr class="border-t">
        <td class="py-2 px-3">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
        <td class="py-2 px-3 font-mono">{{ $r->receipt_no }}</td>
        <td class="py-2 px-3 uppercase">{{ $r->channel }}</td>
        <td class="py-2 px-3">{{ $r->program->name ?? 'General Fund' }}</td>
        <td class="py-2 px-3 text-right">Rp {{ number_format($r->amount,0,',','.') }}</td>
        <td class="py-2 px-3">{{ $r->status }}</td>
      </tr>
      @empty
      <tr><td colspan="6" class="py-6 text-center text-gray-500">Belum ada donasi</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $rows->links() }}</div>
@endsection
