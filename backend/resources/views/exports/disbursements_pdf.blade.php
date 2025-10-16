<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Laporan Penyaluran</title>
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
  <h2>Laporan Penyaluran</h2>
  <div class="meta">Periode: {{ $from ?: '-' }} s/d {{ $to ?: '-' }}</div>
  <table>
    <thead>
      <tr>
        <th>Kode</th>
        <th>Penerima</th>
        <th>Program</th>
        <th class="right">Jumlah</th>
        <th>Status</th>
        <th>Tanggal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $r)
      <tr>
        <td>{{ $r->code }}</td>
        <td>{{ $r->beneficiary->name ?? '' }}</td>
        <td>{{ $r->program->name ?? '' }}</td>
        <td class="right">{{ number_format($r->amount,0,',','.') }}</td>
        <td>{{ $r->status }}</td>
        <td>{{ optional($r->created_at)->format('Y-m-d') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>

