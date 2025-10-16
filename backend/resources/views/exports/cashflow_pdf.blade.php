<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Laporan Arus Kas</title>
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
  <h2>Arus Kas</h2>
  <div class="meta">Periode: {{ $from ?: '-' }} s/d {{ $to ?: '-' }}</div>
  <table>
    <thead>
      <tr>
        <th>Periode</th>
        <th class="right">Debit</th>
        <th class="right">Kredit</th>
      </tr>
    </thead>
    <tbody>
      @foreach($series as $r)
      <tr>
        <td>{{ $r->ym }}</td>
        <td class="right">{{ number_format($r->debit,0,',','.') }}</td>
        <td class="right">{{ number_format($r->kredit,0,',','.') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>

