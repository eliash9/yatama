<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Realisasi vs Anggaran</title>
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
  <h2>Realisasi vs Anggaran</h2>
  <div>Unit: {{ $unitName ?: '-' }} | Periode: {{ $periodeName ?: '-' }}</div>
  <table>
    <thead><tr><th>Kode Akun</th><th class="right">Pagu</th><th class="right">Realisasi</th><th class="right">Sisa</th></tr></thead>
    <tbody>
      @foreach($rows as $r)
      <tr>
        <td>{{ $r['account_code'] }}</td>
        <td class="right">{{ number_format($r['pagu'],0,',','.') }}</td>
        <td class="right">{{ number_format($r['realisasi'],0,',','.') }}</td>
        <td class="right">{{ number_format($r['sisa'],0,',','.') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>

