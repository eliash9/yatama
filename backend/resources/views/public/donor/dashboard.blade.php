@extends('layouts.public')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-xl font-semibold">Akun Donatur</h1>
  <form method="POST" action="{{ route('public.donor.logout') }}">@csrf <button class="text-sm text-gray-600 underline">Keluar</button></form>
  </div>

<div class="bg-white rounded-xl shadow p-4 mb-4">
  <div class="text-sm text-gray-500">Total Donasi</div>
  <div class="text-2xl font-semibold">Rp {{ number_format($total,0,',','.') }}</div>
</div>

<div class="bg-white rounded-xl shadow p-4 mb-4">
  <div class="font-medium mb-2">Per Program</div>
  <ul class="text-sm divide-y">
    @foreach($byProgram as $bp)
      @php $name = $bp->pid ? ($programNames[$bp->pid] ?? 'Program #'.$bp->pid) : 'General Fund'; @endphp
      <li class="flex justify-between py-2"><span>{{ $name }}</span><span>Rp {{ number_format($bp->total,0,',','.') }}</span></li>
    @endforeach
  </ul>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
  <div class="p-4 font-medium">Donasi Terakhir</div>
  <table class="min-w-full text-sm">
    <thead class="text-gray-500"><tr class="text-left"><th class="py-2 px-3">Tanggal</th><th class="py-2 px-3">Kanal</th><th class="py-2 px-3">Earmark</th><th class="py-2 px-3 text-right">Jumlah</th></tr></thead>
    <tbody>
      @forelse($recent as $r)
      <tr class="border-t">
        <td class="py-2 px-3">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
        <td class="py-2 px-3 uppercase">{{ $r->channel }}</td>
        <td class="py-2 px-3">{{ $r->program->name ?? 'General Fund' }}</td>
        <td class="py-2 px-3 text-right">Rp {{ number_format($r->amount,0,',','.') }}</td>
      </tr>
      @empty
      <tr><td colspan="4" class="py-6 text-center text-gray-500">Belum ada donasi</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection

