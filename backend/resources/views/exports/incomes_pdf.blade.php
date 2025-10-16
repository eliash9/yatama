<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Laporan Penerimaan</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #999; padding:6px; }
    th { background:#f3f4f6; }
    h2 { margin: 0 0 10px 0; }
    .meta { margin-bottom:10px; }
    .right { text-align:right; }
  </style>
  </head>
<body>
  <h2>Laporan Penerimaan</h2>
  <div class="meta">Periode: {{ $from ?: '-' }} s/d {{ $to ?: '-' }}</div>
  <table>
    <thead>
      <tr>
        <th>Tanggal</th>
        <th>No Kwitansi</th>
        <th>Kanal</th>
        <th>Donatur</th>
        <th>Program</th>
        <th class="right">Jumlah</th>
        <th>Status</th>
        <th>Ref</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $r)
      <tr>
        <td>{{ $r->tanggal }}</td>
        <td>{{ $r->receipt_no }}</td>
        <td>{{ strtoupper($r->channel) }}</td>
        <td>{{ $r->donor->name ?? '' }}</td>
        <td>{{ $r->program->name ?? 'General Fund' }}</td>
        <td class="right">{{ number_format($r->amount,0,',','.') }}</td>
        <td>{{ $r->status }}</td>
        <td>{{ $r->ref_no }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>

