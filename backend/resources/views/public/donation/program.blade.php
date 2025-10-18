@extends('layouts.public')
@section('content')
  <div class="mb-3">
    @if($program->banner_url)
      <img src="/{{ $program->banner_url }}" alt="banner" class="w-full h-32 object-cover rounded-xl" />
    @elseif(env('DONATION_BANNER_PLACEHOLDER_URL'))
      <img src="{{ env('DONATION_BANNER_PLACEHOLDER_URL') }}" alt="banner" class="w-full h-32 object-cover rounded-xl" />
    @else
      <div class="h-28 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500"></div>
    @endif
  </div>
  <div class="mb-3">
    <h1 class="text-xl font-semibold">{{ $program->name }}</h1>
    <p class="text-sm text-gray-600">{{ $program->description }}</p>
  </div>

  <div class="bg-white rounded-xl shadow p-4 mb-4 text-sm">
    <div class="flex justify-between"><span>Target</span><span>Rp {{ number_format($t,0,',','.') }}</span></div>
    <div class="flex justify-between"><span>Terkumpul</span><span>Rp {{ number_format($collected,0,',','.') }}</span></div>
    @if(!is_null($pct))
    <div class="mt-2 h-2 bg-gray-100 rounded-full overflow-hidden">
      <div class="h-2 bg-blue-600" style="width: {{ $pct }}%"></div>
    </div>
    @endif
  </div>

  <div class="bg-white rounded-xl shadow p-4 mb-4">
    <h3 class="font-medium mb-2">Dukung Program</h3>
    <form method="POST" action="{{ route('public.donation.checkout') }}" class="space-y-3">
      @csrf
      <input type="hidden" name="program_id" value="{{ $program->id }}" />
      <div>
        <label class="block text-sm text-gray-600 mb-1">Nominal (Rp)</label>
        <input type="number" name="amount" min="10000" required placeholder="50000" class="w-full border rounded-lg px-4 py-3" />
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-2">Kanal Pembayaran</label>
        <div class="grid grid-cols-2 gap-3 text-sm">
          <label class="border rounded-lg px-3 py-3 flex items-center gap-2">
            <input type="radio" name="channel" value="transfer" required /> <span>Transfer</span>
          </label>
          <label class="border rounded-lg px-3 py-3 flex items-center gap-2">
            <input type="radio" name="channel" value="qris" required /> <span>QRIS</span>
          </label>
        </div>
        @if(!$qrisUrl)
          <p class="text-xs text-amber-600 mt-1">QRIS statis belum dikonfigurasi (DONATION_QRIS_URL).</p>
        @endif
      </div>
      <div class="grid grid-cols-1 gap-3">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Nama</label>
          <input name="name" placeholder="Nama lengkap" class="w-full border rounded-lg px-4 py-3" />
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Email</label>
          <input name="email" type="email" placeholder="email@anda.com" class="w-full border rounded-lg px-4 py-3" />
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">No. HP</label>
          <input name="phone" placeholder="08xxxx" class="w-full border rounded-lg px-4 py-3" />
        </div>
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Catatan (opsional)</label>
        <textarea name="notes" rows="2" class="w-full border rounded-lg px-4 py-3" placeholder="Doa/dukungan untuk program"></textarea>
      </div>
      <button class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-3 text-base">Donasi Sekarang</button>
    </form>
  </div>

  <div class="mb-6">
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
@endsection
