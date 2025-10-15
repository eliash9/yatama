@extends('layouts.app')
@section('content')
@include('partials.flash')

<div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">Transaksi Kas/Bank</h2>
  <a href="{{ route('finance.transactions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">Catat Transaksi</a>
</div>

<form method="GET" class="mb-4 flex gap-2 flex-wrap text-sm">
  <input type="date" name="from" value="{{ request('from') }}" class="border rounded px-3 py-2" />
  <input type="date" name="to" value="{{ request('to') }}" class="border rounded px-3 py-2" />
  <input name="akun_kas" value="{{ request('akun_kas') }}" placeholder="Akun kas/bank" class="border rounded px-3 py-2" />
  <select name="jenis" class="border rounded px-3 py-2">
    <option value="">Semua</option>
    <option value="debit" @selected(request('jenis')==='debit')>Debit</option>
    <option value="kredit" @selected(request('jenis')==='kredit')>Kredit</option>
  </select>
  <button class="px-3 py-2 bg-gray-100 rounded">Filter</button>
  <a href="{{ route('finance.transactions.index') }}" class="px-3 py-2 underline">Reset</a>
</form>

<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="text-gray-600"><tr class="text-left">
      <th class="py-2 px-3">Tanggal</th>
      <th class="py-2 px-3">Jenis</th>
      <th class="py-2 px-3">Akun</th>
      <th class="py-2 px-3">Jumlah</th>
      <th class="py-2 px-3">Memo</th>
      <th class="py-2 px-3 w-32">Aksi</th>
    </tr></thead>
    <tbody>
      @forelse($rows as $r)
      <tr class="border-t">
        <td class="py-2 px-3">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
        <td class="py-2 px-3 capitalize">{{ $r->jenis }}</td>
        <td class="py-2 px-3">{{ $r->akun_kas }}</td>
        <td class="py-2 px-3">Rp {{ number_format($r->amount,0,',','.') }}</td>
        <td class="py-2 px-3">{{ $r->memo }}</td>
        <td class="py-2 px-3">
          <form method="POST" action="{{ route('finance.transactions.destroy',$r) }}" onsubmit="return confirm('Hapus transaksi?')">
            @csrf @method('DELETE')
            <button class="px-2 py-1 bg-red-50 text-red-700 rounded">Hapus</button>
          </form>
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

