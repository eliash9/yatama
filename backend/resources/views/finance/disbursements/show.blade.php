@extends('layouts.app')
@section('content')
@include('partials.flash')

<div class="mb-4">
  <a href="{{ route('finance.disbursements.index') }}" class="text-sm text-gray-600 hover:underline">← Kembali</a>
  @if(in_array($row->status,['draft']))
    <form method="POST" action="{{ route('finance.disbursements.submit',$row) }}" class="inline ml-2">@csrf <button class="px-3 py-1.5 bg-blue-600 text-white rounded">Kirim</button></form>
    <a href="{{ route('finance.disbursements.edit',$row) }}" class="ml-2 px-3 py-1.5 bg-gray-100 rounded">Ubah</a>
  @endif
  @if(in_array($row->status,['submitted','assessed']))
    <form method="POST" action="{{ route('finance.disbursements.assess',$row) }}" class="inline ml-2">@csrf <button class="px-3 py-1.5 bg-gray-100 rounded">Tandai Asesmen</button></form>
  @endif
  @if(in_array($row->status,['assessed','program_verified']))
    <form method="POST" action="{{ route('finance.disbursements.verify_program',$row) }}" class="inline ml-2">@csrf <button class="px-3 py-1.5 bg-emerald-600 text-white rounded">Verifikasi Program</button></form>
  @endif
  @if(in_array($row->status,['program_verified','finance_verified']))
    <form method="POST" action="{{ route('finance.disbursements.verify_finance',$row) }}" class="inline ml-2">@csrf <button class="px-3 py-1.5 bg-amber-600 text-white rounded">Verifikasi Keuangan</button></form>
  @endif
  @if(in_array($row->status,['finance_verified','approved']))
    <form method="POST" action="{{ route('finance.disbursements.approve',$row) }}" class="inline ml-2">@csrf <button class="px-3 py-1.5 bg-indigo-600 text-white rounded">Setujui</button></form>
  @endif
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  <div class="md:col-span-2">
    <div class="bg-white rounded shadow p-4 mb-4">
      <h2 class="text-lg font-semibold mb-2">Detail Pengajuan</h2>
      <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
        <div><dt class="text-gray-500">Kode</dt><dd class="font-mono">{{ $row->code }}</dd></div>
        <div><dt class="text-gray-500">Status</dt><dd>{{ $row->status }}</dd></div>
        <div><dt class="text-gray-500">Program</dt><dd>{{ $row->program->name }}</dd></div>
        <div><dt class="text-gray-500">Penerima</dt><dd>{{ $row->beneficiary->name }}</dd></div>
        <div><dt class="text-gray-500">Jumlah</dt><dd>Rp {{ number_format($row->amount,0,',','.') }}</dd></div>
        <div><dt class="text-gray-500">Preferensi</dt><dd>{{ $row->method_preference ?: '-' }}</dd></div>
        <div class="md:col-span-2"><dt class="text-gray-500">Tujuan</dt><dd>{{ $row->purpose ?: '-' }}</dd></div>
      </dl>
    </div>

    <div class="bg-white rounded shadow p-4">
      <h3 class="font-medium mb-2">Pembayaran</h3>
      @if($row->status==='approved')
      <form method="POST" action="{{ route('finance.disbursements.pay',$row) }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
        @csrf
        <div>
          <label class="block text-gray-600 mb-1">Kanal</label>
          <select name="channel" class="w-full border rounded px-3 py-2" required>
            @foreach(['cash'=>'Tunai','transfer'=>'Transfer','ewallet'=>'E-Wallet'] as $k=>$v)
              <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-gray-600 mb-1">Akun (jika transfer)</label>
          <select name="account_id" class="w-full border rounded px-3 py-2">
            <option value="">-</option>
            @foreach($accounts as $a)
              <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->code }})</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-gray-600 mb-1">Jumlah (Rp)</label>
          <input type="number" name="amount" min="1" value="{{ $row->amount }}" class="w-full border rounded px-3 py-2" required />
        </div>
        <div>
          <label class="block text-gray-600 mb-1">Nama Penerima</label>
          <input name="recipient_name" value="{{ $row->beneficiary->name }}" class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-gray-600 mb-1">Bank</label>
          <input name="bank_name" class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-gray-600 mb-1">No Rekening</label>
          <input name="account_no" class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-gray-600 mb-1">ID E-Wallet</label>
          <input name="ewallet_id" class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-gray-600 mb-1">Ref/No Transaksi</label>
          <input name="ref_no" class="w-full border rounded px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-gray-600 mb-1">Tanda Terima (upload)</label>
          <input type="file" name="receipt" accept="image/*,application/pdf" class="w-full border rounded px-3 py-2" />
        </div>
        <div class="md:col-span-2">
          <button class="px-4 py-2 bg-emerald-600 text-white rounded">Eksekusi Pembayaran</button>
        </div>
      </form>
      @endif

      @if($row->payments->isNotEmpty())
        <div class="mt-4">
          <table class="min-w-full text-sm">
            <thead class="text-gray-500"><tr class="text-left"><th class="py-1 pr-4">Tgl</th><th class="py-1 pr-4">Kanal</th><th class="py-1 pr-4 text-right">Jumlah</th><th class="py-1 pr-4">Ref</th><th class="py-1 pr-4">Tanda Terima</th></tr></thead>
            <tbody>
              @foreach($row->payments as $p)
              <tr class="border-t">
                <td class="py-1 pr-4">{{ $p->paid_at?\Carbon\Carbon::parse($p->paid_at)->format('d M Y'):'-' }}</td>
                <td class="py-1 pr-4">{{ $p->channel }}</td>
                <td class="py-1 pr-4 text-right">Rp {{ number_format($p->amount,0,',','.') }}</td>
                <td class="py-1 pr-4">{{ $p->ref_no }}</td>
                <td class="py-1 pr-4">@if($p->receipt_url)<a href="/{{ $p->receipt_url }}" class="text-blue-600 hover:underline" target="_blank">Lihat</a>@else - @endif</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
  <div>
    <div class="bg-white rounded shadow p-4 mb-4">
      <h3 class="font-medium mb-2">Lampiran Dokumen</h3>
      <form method="POST" action="{{ route('finance.incomes.store') }}" enctype="multipart/form-data" class="text-sm hidden"></form>
      <form method="POST" action="{{ route('finance.disbursements.pay',$row) }}" enctype="multipart/form-data" class="hidden"></form>
      <form method="POST" action="{{ url('/upload-disbursement-'.$row->id) }}" enctype="multipart/form-data" onsubmit="return false;"></form>
      @if($attachments->isEmpty())
        <p class="text-sm text-gray-500">Belum ada lampiran.</p>
      @else
        <ul class="text-sm space-y-2">
          @foreach($attachments as $a)
            <li class="flex items-center justify-between">
              <a class="text-blue-600 hover:underline" href="/{{ $a->url }}" target="_blank">{{ $a->filename }}</a>
              <span class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($a->uploaded_at)->format('d M Y H:i') }}</span>
            </li>
          @endforeach
        </ul>
      @endif
      <p class="text-xs text-gray-500 mt-2">Lampiran ditambahkan otomatis saat upload tanda terima pembayaran.</p>
    </div>

    <div class="bg-white rounded shadow p-4">
      <h3 class="font-medium mb-2">Verifikasi</h3>
      <ol class="text-sm list-decimal ml-5 space-y-1">
        <li>Program: {{ optional($row->approvals->firstWhere('level',1))->status ?? 'pending' }}</li>
        <li>Keuangan: {{ optional($row->approvals->firstWhere('level',2))->status ?? 'pending' }}</li>
        <li>Approver: {{ optional($row->approvals->firstWhere('level',3))->status ?? 'pending' }}</li>
      </ol>
    </div>
  </div>
</div>
@endsection
