@extends('layouts.public')
@section('content')
  <!-- Hero / Slider -->
  <div class="mb-4">
    <div class="overflow-x-auto whitespace-nowrap space-x-3 snap-x">
      @foreach(($programs ?? collect())->take(5) as $p)
        <a href="{{ route('public.donation.program',$p) }}" class="inline-block w-72 snap-start">
          @if($p->banner_url)
            <img src="/{{ $p->banner_url }}" class="w-72 h-36 object-cover rounded-xl" />
          @elseif(env('DONATION_BANNER_PLACEHOLDER_URL'))
            <img src="{{ env('DONATION_BANNER_PLACEHOLDER_URL') }}" class="w-72 h-36 object-cover rounded-xl" />
          @else
            <div class="w-72 h-36 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500"></div>
          @endif
          <div class="mt-1 text-sm font-medium">{{ $p->name }}</div>
        </a>
      @endforeach
    </div>
  </div>

  <!-- Shortcut Icons -->
  <div class="grid grid-cols-4 gap-3 text-center text-xs mb-4">
    <a href="#programs" class="bg-white rounded-lg shadow p-3">ðŸŽ¯<div>Program</div></a>
    <a href="{{ route('public.donation.status') }}" class="bg-white rounded-lg shadow p-3">ðŸ§¾<div>Status</div></a>
    <a href="{{ route('public.donation.account.login') }}" class="bg-white rounded-lg shadow p-3">ðŸ‘¤<div>Akun</div></a>
    <a href="{{ route('public.donation.index') }}" class="bg-white rounded-lg shadow p-3">ðŸ’–<div>Donasi</div></a>
  </div>

  <!-- CTA Register/Login -->
  <div class="bg-blue-600 text-white rounded-xl p-4 mb-4">
    <div class="font-medium">Jadilah Donatur Terdaftar</div>
    <div class="text-sm text-blue-100">Pantau donasi dan dampaknya.</div>
    <a href="{{ route('public.donation.account.login') }}" class="inline-block mt-2 bg-white text-blue-700 rounded px-3 py-1.5 text-sm">Daftar / Masuk</a>
  </div>

  <!-- Program List -->
  <div id="programs" class="mt-6">
    <h3 class="font-medium mb-2">Program</h3>
    <div class="space-y-3">
      @forelse($programs as $p)
        @php $t = (int)($p->target_amount ?? 0); $col = (int)($collected[$p->id] ?? 0); $pct = $t>0 ? min(100, intval($col*100/$t)) : null; @endphp
        <div class="bg-white rounded-xl shadow overflow-hidden">
          <a href="{{ route('public.donation.program',$p) }}">
            @if($p->banner_url)
              <img src="/{{ $p->banner_url }}" alt="banner" class="w-full h-24 object-cover" />
            @elseif(env('DONATION_BANNER_PLACEHOLDER_URL'))
              <img src="{{ env('DONATION_BANNER_PLACEHOLDER_URL') }}" alt="banner" class="w-full h-24 object-cover" />
            @else
              <div class="h-24 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
            @endif
          </a>
          <div class="p-4">
            <a href="{{ route('public.donation.program',$p) }}" class="font-medium mb-1 inline-block">{{ $p->name }}</a>
            <div class="text-xs text-gray-500">Target: {{ $t ? ('Rp '.number_format($t,0,',','.')) : '-' }}</div>
            <div class="text-xs text-gray-500">Terkumpul: Rp {{ number_format($col,0,',','.') }}</div>
            @if(!is_null($pct))
            <div class="mt-2 h-2 bg-gray-100 rounded-full overflow-hidden">
              <div class="h-2 bg-blue-600" style="width: {{ $pct }}%"></div>
            </div>
            @endif
            <div class="mt-3">
              <a href="{{ route('public.donation.program',$p) }}" class="inline-block bg-blue-600 text-white rounded px-3 py-1.5 text-sm">Lihat Program</a>
            </div>
          </div>
        </div>
      @empty
        <div class="text-sm text-gray-500">Belum ada program.</div>
      @endforelse
    </div>
  </div>

  <!-- Berita Penyaluran / Blogging -->
  <div class="mt-6">
    <h3 class="font-medium mb-2">Berita Penyaluran</h3>
    <div class="bg-white rounded-xl shadow divide-y">
      @forelse(($recentDisb ?? []) as $d)
        <div class="p-3 text-sm">
          <div class="flex justify-between">
            <div class="font-medium">{{ $d->program->name ?? 'Program' }}</div>
            <div>Rp {{ number_format($d->amount,0,',','.') }}</div>
          </div>
          <div class="text-xs text-gray-500">{{ $d->beneficiary->name ?? 'Penerima' }} â€” {{ optional($d->updated_at)->format('d M Y') }}</div>
          <div class="text-xs text-gray-600 mt-1">Kode: {{ $d->code }}</div>
        </div>
      @empty
        <div class="p-3 text-sm text-gray-500">Belum ada berita penyaluran</div>
      @endforelse
    </div>
  </div>

  <!-- Donatur Terbaru -->
  <div class="mt-6">
    <h3 class="font-medium mb-2">Donatur Terbaru</h3>
    <div class="bg-white rounded-xl shadow divide-y">
      @forelse($recentDonors as $r)
        <div class="flex justify-between items-center p-3 text-sm">
          <div>
            <div class="font-medium">{{ $r->donor->name ?? 'Hamba Allah' }}</div>
            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</div>
          </div>
          <div class="font-medium">Rp {{ number_format($r->amount,0,',','.') }}</div>
        </div>
      @empty
        <div class="p-3 text-sm text-gray-500">Belum ada donasi</div>
      @endforelse
    </div>
  </div>

  <!-- FAQ -->
  <div class="mt-6">
    <h3 class="font-medium mb-2">FAQ</h3>
    <div class="bg-white rounded-xl shadow divide-y">
      <div class="p-3">
        <div class="font-medium text-sm">Bagaimana cara berdonasi?</div>
        <div class="text-xs text-gray-600 mt-1">Pilih program, masukkan nominal di halaman program, lalu pilih kanal pembayaran (Transfer/QRIS).</div>
      </div>
      <div class="p-3">
        <div class="font-medium text-sm">Bagaimana cek status donasi?</div>
        <div class="text-xs text-gray-600 mt-1">Gunakan menu Status dan masukkan kode referensi (ref) yang tampil saat checkout.</div>
      </div>
      <div class="p-3">
        <div class="font-medium text-sm">Apakah ada kwitansi?</div>
        <div class="text-xs text-gray-600 mt-1">Setelah donasi terverifikasi, Anda dapat mengunduh kwitansi dari halaman status.</div>
      </div>
    </div>
  </div>
@endsection




