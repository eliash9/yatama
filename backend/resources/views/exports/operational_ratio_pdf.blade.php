<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Rasio Operasional</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #999; padding:6px; }
    th { background:#f3f4f6; }
    h2 { margin: 0 0 10px 0; }
    .right { text-align:right; }
  </style>
  </head>
<body>
  <h2>Rasio Operasional</h2>
  <div>Periode: {{ $from ?: '-' }} s/d {{ $to ?: '-' }}</div>
  @php $total = max(1, (int)$ops + (int)$programSpend); $ratio = round(((int)$ops/$total)*100,2); @endphp
  <table>
    <thead><tr><th>Kategori</th><th class="right">Jumlah</th></tr></thead>
    <tbody>
      <tr><td>Belanja Operasional</td><td class="right">{{ number_format($ops,0,',','.') }}</td></tr>
      <tr><td>Belanja Program</td><td class="right">{{ number_format($programSpend,0,',','.') }}</td></tr>
      <tr><td>Rasio Operasional (%)</td><td class="right">{{ number_format($ratio,2,',','.') }}</td></tr>
    </tbody>
  </table>
</body>
</html>

