@extends('layouts.public')
@section('content')
  <div class="text-center mb-4">
    <h1 class="text-2xl font-semibold">Status Pembayaran</h1>
    <p class="text-sm text-gray-500">Ref: <span class="font-mono">{{ $row->ref_no }}</span></p>
  </div>

  <div class="bg-white rounded-xl shadow p-4 space-y-2 text-sm">
    <div class="flex justify-between"><span>Jumlah</span><span class="font-medium">Rp {{ number_format($row->amount,0,',','.') }}</span></div>
    <div class="flex justify-between"><span>Kanal</span><span class="uppercase">{{ $row->channel }}</span></div>
    <div class="flex justify-between"><span>Status</span><span class="capitalize">{{ $row->status }}</span></div>
    <div class="flex justify-between"><span>Tanggal</span><span>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</span></div>
  </div>

  @if($row->status==='recorded')
  <div class="mt-4 bg-white rounded-xl shadow p-4 text-sm">
    @if($row->channel==='transfer')
      <h3 class="font-medium mb-2">Instruksi Transfer</h3>
      @php $prov = strtoupper($pay['provider'] ?? ''); $info = $banks[$prov] ?? null; @endphp
      @if($info && $info['account'])
      <div class="space-y-2">
        <div class="flex justify-between"><span>Nominal</span><span class="font-medium">Rp {{ number_format($row->amount,0,',','.') }}</span></div>
        <div class="flex justify-between items-center"><span>Bank</span><span class="font-medium">{{ $prov }}</span></div>
        <div class="flex justify-between items-center"><span>No. Rekening</span><span>
          <span id="accno" class="font-mono">{{ $info['account'] }}</span>
          <button onclick="copyText('accno')" class="ml-2 text-blue-600 underline">Copy</button>
        </span></div>
        <div class="flex justify-between items-center"><span>Atas Nama</span><span class="font-medium">{{ $info['name'] }}</span></div>
        <div class="flex justify-between items-center"><span>Berita/Ref</span><span>
          <span id="ref" class="font-mono">{{ $row->ref_no }}</span>
          <button onclick="copyText('ref')" class="ml-2 text-blue-600 underline">Copy</button>
        </span></div>
      </div>
      @else
      <p class="text-amber-700">Rekening bank belum dikonfigurasi untuk provider ini.</p>
      @endif
      <ol class="list-decimal ml-5 mt-3 space-y-1">
        <li>Transfer sesuai nominal ke rekening di atas.</li>
        <li>Tulis ref <span class="font-mono">{{ $row->ref_no }}</span> di berita.</li>
        <li>Setelah transfer, sistem akan memadankan otomatis.</li>
      </ol>
    @elseif($row->channel==='ewallet')
      <h3 class="font-medium mb-2">Instruksi E-Wallet</h3>
      @php $prov = strtoupper($pay['provider'] ?? ''); $info = $ewallets[$prov] ?? null; @endphp
      @if($info && $info['number'])
      <div class="space-y-2">
        <div class="flex justify-between"><span>Nominal</span><span class="font-medium">Rp {{ number_format($row->amount,0,',','.') }}</span></div>
        <div class="flex justify-between items-center"><span>Provider</span><span class="font-medium">{{ $prov }}</span></div>
        <div class="flex justify-between items-center"><span>Tujuan</span><span>
          <span id="ewnum" class="font-mono">{{ $info['number'] }}</span>
          <button onclick="copyText('ewnum')" class="ml-2 text-blue-600 underline">Copy</button>
        </span></div>
        <div class="flex justify-between items-center"><span>Nama</span><span class="font-medium">{{ $info['name'] }}</span></div>
        <div class="flex justify-between items-center"><span>Catatan</span><span>
          <span id="ref2" class="font-mono">{{ $row->ref_no }}</span>
          <button onclick="copyText('ref2')" class="ml-2 text-blue-600 underline">Copy</button>
        </span></div>
      </div>
      @else
      <p class="text-amber-700">Akun e-wallet belum dikonfigurasi untuk provider ini.</p>
      @endif
    @elseif($row->channel==='qris')
      <h3 class="font-medium mb-2">Scan QRIS</h3>
      @if($qrisUrl)
        <img src="{{ $qrisUrl }}" alt="QRIS" class="w-64 h-64 mx-auto rounded border" />
      @else
        <p class="text-sm text-amber-600">QRIS belum dikonfigurasi. Hubungi admin.</p>
      @endif
      <p class="text-xs text-gray-500 mt-2">Gunakan berita: <span class="font-mono">{{ $row->ref_no }}</span></p>
    @endif
  </div>
  @endif

  <div class="mt-4 text-center">
    @if($row->status==='matched')
      <a href="{{ route('public.donation.thanks',['ref'=>$row->ref_no]) }}" class="inline-block bg-emerald-600 text-white rounded-lg px-4 py-2">Lanjut ke Terima Kasih</a>
    @else
      <a href="{{ route('public.donation.status',['ref'=>$row->ref_no]) }}" class="inline-block text-blue-600 underline">Refresh Status</a>
    @endif
  </div>

  <script>
    function copyText(id){
      const el = document.getElementById(id);
      if(!el) return;
      const txt = el.innerText || el.textContent;
      navigator.clipboard && navigator.clipboard.writeText(txt);
    }
  </script>
@endsection
