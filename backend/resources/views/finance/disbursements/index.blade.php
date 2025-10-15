@extends('layouts.app')
@section('content')
@include('partials.flash')

<div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">Penyaluran</h2>
  <a href="{{ route('finance.disbursements.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">Buat Pengajuan</a>
</div>

<form method="GET" class="mb-4 flex gap-2 flex-wrap text-sm">
  <select name="status" class="border rounded px-3 py-2">
    <option value="">Semua Status</option>
    @foreach(['draft','submitted','assessed','program_verified','finance_verified','approved','paid','rejected'] as $st)
      <option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>
    @endforeach
  </select>
  <select name="program_id" class="border rounded px-3 py-2">
    <option value="">Semua Program</option>
    @foreach($programs as $p)
      <option value="{{ $p->id }}" @selected(request('program_id')==$p->id)>{{ $p->name }}</option>
    @endforeach
  </select>
  <button class="px-3 py-2 bg-gray-100 rounded">Filter</button>
  <a href="{{ route('finance.disbursements.index') }}" class="px-3 py-2 underline">Reset</a>
</form>

<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="text-gray-600"><tr class="text-left">
      <th class="py-2 px-3">Kode</th>
      <th class="py-2 px-3">Program</th>
      <th class="py-2 px-3">Penerima</th>
      <th class="py-2 px-3">Jumlah</th>
      <th class="py-2 px-3">Status</th>
      <th class="py-2 px-3 w-36">Aksi</th>
    </tr></thead>
    <tbody>
      @forelse($rows as $r)
      <tr class="border-t">
        <td class="py-2 px-3 font-mono">{{ $r->code }}</td>
        <td class="py-2 px-3">{{ $r->program->name }}</td>
        <td class="py-2 px-3">{{ $r->beneficiary->name }}</td>
        <td class="py-2 px-3">Rp {{ number_format($r->amount,0,',','.') }}</td>
        <td class="py-2 px-3">{{ $r->status }}</td>
        <td class="py-2 px-3">
          <a class="px-2 py-1 bg-white border rounded" href="{{ route('finance.disbursements.show',$r) }}">Detail</a>
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

