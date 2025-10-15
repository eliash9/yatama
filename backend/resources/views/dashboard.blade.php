@extends('layouts.app')

@section('content')
<div class="mb-6">
  <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
      <select name="unit_id" class="w-full border rounded px-3 py-2">
        <option value="">Semua Unit</option>
        @foreach($units as $u)
        <option value="{{ $u->id }}" @selected($filters['unit_id']==$u->id)>{{ $u->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
      <select name="periode_id" class="w-full border rounded px-3 py-2">
        <option value="">Semua Periode</option>
        @foreach($periodes as $p)
        <option value="{{ $p->id }}" @selected($filters['periode_id']==$p->id)>{{ $p->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="md:col-span-2">
      <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Terapkan Filter</button>
      <a href="{{ route('dashboard') }}" class="ml-2 text-sm text-gray-600 underline">Reset</a>
    </div>
  </form>
  @role('admin')
  <form method="POST" action="{{ route('admin.demo_reset') }}" class="mt-3" onsubmit="return confirm('Reset demo akan menghapus dan mengisi ulang data. Lanjutkan?')">
    @csrf
    <button class="px-3 py-2 bg-red-600 text-white rounded">Reset Demo (Admin)</button>
  </form>
  @endrole
  <p class="text-xs text-gray-500 mt-2">Filter diterapkan pada seluruh ringkasan di bawah.</p>
  </div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="bg-white rounded-lg shadow p-4">
    <div class="text-gray-500 text-sm">Total Pagu</div>
    <div class="text-2xl font-semibold mt-1">Rp {{ number_format($totalPagu,0,',','.') }}</div>
  </div>
  <div class="bg-white rounded-lg shadow p-4">
    <div class="text-gray-500 text-sm">Total Disetujui/Dicairkan</div>
    <div class="text-2xl font-semibold mt-1">Rp {{ number_format($totalDimintaApproved,0,',','.') }}</div>
  </div>
  <div class="bg-white rounded-lg shadow p-4">
    <div class="text-gray-500 text-sm">Sisa Pagu</div>
    <div class="text-2xl font-semibold mt-1">Rp {{ number_format($sisaPagu,0,',','.') }}</div>
  </div>
  <div class="bg-white rounded-lg shadow p-4">
    <div class="text-gray-500 text-sm">Arus Kas (Debit)</div>
    <div class="text-xl font-semibold mt-1">Rp {{ number_format($arusKas['debit'] ?? 0,0,',','.') }}</div>
  </div>
  <div class="bg-white rounded-lg shadow p-4">
    <div class="text-gray-500 text-sm">Arus Kas (Kredit)</div>
    <div class="text-xl font-semibold mt-1">Rp {{ number_format($arusKas['kredit'] ?? 0,0,',','.') }}</div>
  </div>
  <div class="bg-white rounded-lg shadow p-4">
    <div class="text-gray-500 text-sm">Pengajuan (Ringkas)</div>
    <ul class="mt-2 text-sm">
      @php $all = ['draft','diajukan','ditinjau','disetujui','ditolak','dicairkan','selesai']; @endphp
      @foreach($all as $st)
        <li class="flex justify-between border-b last:border-b-0 py-1">
          <span class="capitalize">{{ $st }}</span>
          <span class="font-medium">{{ $counts[$st] ?? 0 }}</span>
        </li>
      @endforeach
    </ul>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
  <div class="bg-white rounded-lg shadow p-4">
    <div class="font-medium mb-2">Distribusi Status Pengajuan</div>
    <canvas id="statusChart" height="120"></canvas>
  </div>
  <div class="bg-white rounded-lg shadow p-4">
    <div class="font-medium mb-2">Arus Kas</div>
    <canvas id="cashChart" height="120"></canvas>
  </div>
  <div class="bg-white rounded-lg shadow p-4">
    <div class="font-medium mb-2">Pemasukan per Kanal</div>
    <canvas id="incomeChannelChart" height="120"></canvas>
  </div>
  <div class="bg-white rounded-lg shadow p-4">
    <div class="font-medium mb-2">Top Program (Pemasukan)</div>
    <table class="min-w-full text-sm">
      <thead><tr class="text-left text-gray-500"><th class="py-1 pr-4">Program</th><th class="py-1 pr-4">Total</th></tr></thead>
      <tbody>
      @forelse($incomeByProgram as $ip)
        @php $name = $ip->pid ? ($programNames[$ip->pid] ?? 'Program #'.$ip->pid) : 'General Fund'; @endphp
        <tr class="border-t">
          <td class="py-2 pr-4">{{ $name }}</td>
          <td class="py-2 pr-4">Rp {{ number_format($ip->total,0,',','.') }}</td>
        </tr>
      @empty
        <tr><td colspan="2" class="py-4 text-gray-500">Belum ada pemasukan</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <script>
    const counts = @json($counts);
    const labels = ['draft','diajukan','ditinjau','disetujui','ditolak','dicairkan','selesai'];
    const dataCounts = labels.map(l => counts[l] ?? 0);
    const ctx1 = document.getElementById('statusChart');
    new Chart(ctx1, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{ data: dataCounts, backgroundColor: ['#94a3b8','#60a5fa','#fbbf24','#22c55e','#ef4444','#14b8a6','#6366f1'] }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
    });

    const ctx2 = document.getElementById('cashChart');
    new Chart(ctx2, {
      type: 'bar',
      data: {
        labels: ['Debit','Kredit'],
        datasets: [{ data: [{{ (int)($arusKas['debit'] ?? 0) }}, {{ (int)($arusKas['kredit'] ?? 0) }}], backgroundColor: ['#22c55e','#ef4444'] }]
      },
      options: { plugins: { legend: { display: false } } }
    });

    const incomeByChannel = @json($incomeByChannel ?? []);
    const ichLabels = Object.keys(incomeByChannel).map(k => k.toUpperCase());
    const ichData = Object.values(incomeByChannel).map(v => Number(v));
    const ctx3 = document.getElementById('incomeChannelChart');
    new Chart(ctx3, {
      type: 'bar',
      data: { labels: ichLabels, datasets: [{ data: ichData, backgroundColor: '#60a5fa' }] },
      options: { plugins: { legend: { display: false } } }
    });
  </script>
</div>

<div class="bg-white rounded-lg shadow">
  <div class="p-4 border-b font-medium">Pengajuan Terbaru</div>
  <div class="p-4 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-gray-500">
          <th class="py-2 pr-4">Kode</th>
          <th class="py-2 pr-4">Judul</th>
          <th class="py-2 pr-4">Status</th>
          <th class="py-2 pr-4">Total</th>
          <th class="py-2 pr-4">Tanggal</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recent as $r)
        <tr class="border-t">
          <td class="py-2 pr-4 font-medium">{{ $r->kode }}</td>
          <td class="py-2 pr-4">{{ $r->judul }}</td>
          <td class="py-2 pr-4 capitalize">{{ $r->status }}</td>
          <td class="py-2 pr-4">Rp {{ number_format($r->total_diminta,0,',','.') }}</td>
          <td class="py-2 pr-4">{{ $r->created_at->format('d M Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="py-6 text-center text-gray-500">Belum ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
