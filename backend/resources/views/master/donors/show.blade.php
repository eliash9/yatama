@extends('layouts.app')
@section('content')
<div class="mb-4">
  <a href="{{ route('master.donors.index') }}" class="text-sm text-gray-600 hover:underline">← Kembali</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  <div class="md:col-span-2">
    <div class="bg-white rounded shadow p-4 mb-4">
      <h2 class="text-lg font-semibold mb-2">Profil Donatur</h2>
      <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
        <div><dt class="text-gray-500">Kode</dt><dd class="font-mono">{{ $donor->code }}</dd></div>
        <div><dt class="text-gray-500">Tipe</dt><dd class="capitalize">{{ $donor->type }}</dd></div>
        <div><dt class="text-gray-500">Nama</dt><dd>{{ $donor->name }}</dd></div>
        <div><dt class="text-gray-500">Kontak</dt><dd>{{ $donor->email }} / {{ $donor->phone }}</dd></div>
        <div class="md:col-span-2"><dt class="text-gray-500">Alamat</dt><dd>{{ $donor->address }}</dd></div>
      </dl>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
      <div class="p-4 border-b font-medium">Riwayat Donasi</div>
      <table class="min-w-full text-sm">
        <thead class="text-gray-600"><tr class="text-left"><th class="py-2 px-3">Tgl</th><th class="py-2 px-3">No Kwitansi</th><th class="py-2 px-3">Program</th><th class="py-2 px-3">Jumlah</th></tr></thead>
        <tbody>
          @forelse($rows as $r)
          <tr class="border-t">
            <td class="py-2 px-3">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
            <td class="py-2 px-3 font-mono"><a class="text-blue-600 hover:underline" href="{{ route('finance.incomes.show',$r) }}">{{ $r->receipt_no }}</a></td>
            <td class="py-2 px-3">{{ $r->program->name ?? 'General Fund' }}</td>
            <td class="py-2 px-3">Rp {{ number_format($r->amount,0,',','.') }}</td>
          </tr>
          @empty
          <tr><td colspan="4" class="py-6 text-center text-gray-500">Belum ada donasi</td></tr>
          @endforelse
        </tbody>
      </table>
      <div class="p-4">{{ $rows->links() }}</div>
    </div>
  </div>

  <div>
    <div class="bg-white rounded shadow p-4">
      <h3 class="font-medium mb-2">Ringkasan</h3>
      <div class="text-sm flex justify-between"><span>Total Donasi</span><span class="font-medium">Rp {{ number_format($total,0,',','.') }}</span></div>
      <div class="mt-3">
        <div class="text-sm text-gray-600 mb-1">Impact per Program</div>
        <ul class="text-sm">
          @foreach($byProgram as $bp)
            @php $name = $bp->program_id ? ($programNames[$bp->program_id] ?? 'Program #'.$bp->program_id) : 'General Fund'; @endphp
            <li class="flex justify-between border-t py-1"><span>{{ $name }}</span><span>Rp {{ number_format($bp->total,0,',','.') }}</span></li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

