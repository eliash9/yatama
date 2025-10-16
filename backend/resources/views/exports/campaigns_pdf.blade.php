<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Realisasi Kampanye/Program</title>
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
  <h2>Realisasi Kampanye/Program</h2>
  <table>
    <thead><tr><th>Program</th><th class="right">Terkumpul</th></tr></thead>
    <tbody>
      @foreach($programs as $p)
      <tr>
        <td>{{ $p->name }}</td>
        <td class="right">{{ number_format((int)($totals[$p->id] ?? 0),0,',','.') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>

