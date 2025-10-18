@extends('layouts.public')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-xl font-semibold">Laporan Penyaluran</h1>
  <a href="{{ route('public.donor.dashboard') }}" class="text-sm text-gray-600 underline">Dashboard</a>
</div>

<div class="bg-white rounded-xl shadow p-4">
  <div class="font-medium mb-2">Ringkasan per Program</div>
  <table class="min-w-full text-sm">
    <thead class="text-gray-500"><tr class="text-left"><th class="py-2 px-3">Program</th><th class="py-2 px-3 text-right">Donasi Anda</th><th class="py-2 px-3 text-right">Penyaluran Total</th></tr></thead>
    <tbody>
      @foreach($programs as $p)
        @php $d = optional($byProgramDonor->firstWhere('pid',$p->id))->total ?? 0; $s = (int)($spendByProgram[$p->id] ?? 0); @endphp
        <tr class="border-t">
          <td class="py-2 px-3">{{ $p->name }}</td>
          <td class="py-2 px-3 text-right">Rp {{ number_format($d,0,',','.') }}</td>
          <td class="py-2 px-3 text-right">Rp {{ number_format($s,0,',','.') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection

