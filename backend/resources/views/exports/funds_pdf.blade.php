<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Dana Terikat vs Umum</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width:100%; border-collapse: collapse; margin-bottom:16px; }
    th, td { border:1px solid #999; padding:6px; }
    th { background:#f3f4f6; }
    h2 { margin: 0 0 10px 0; }
    .right { text-align:right; }
  </style>
  </head>
<body>
  <h2>Dana Terikat vs Umum</h2>
  <div>Periode: {{ $from ?: '-' }} s/d {{ $to ?: '-' }}</div>
  <table>
    <thead><tr><th>Kategori</th><th class="right">Jumlah</th></tr></thead>
    <tbody>
      <tr><td>Earmark (Masuk)</td><td class="right">{{ number_format($earmark,0,',','.') }}</td></tr>
      <tr><td>General (Masuk)</td><td class="right">{{ number_format($general,0,',','.') }}</td></tr>
      <tr><td>Program (Keluar)</td><td class="right">{{ number_format($spendProgram,0,',','.') }}</td></tr>
      <tr><td>Umum/Operasional (Keluar)</td><td class="right">{{ number_format($spendGeneral,0,',','.') }}</td></tr>
    </tbody>
  </table>
</body>
</html>

