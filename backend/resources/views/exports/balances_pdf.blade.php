<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Saldo Akun & Program</title>
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
  <h2>Saldo per Akun</h2>
  <table>
    <thead><tr><th>Akun</th><th class="right">Saldo</th></tr></thead>
    <tbody>
      @foreach($accountBalances as $ab)
      <tr>
        <td>{{ $ab['account']->name }} ({{ $ab['account']->code }})</td>
        <td class="right">{{ number_format($ab['balance'],0,',','.') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <h2>Saldo per Program</h2>
  <table>
    <thead><tr><th>Program</th><th class="right">Saldo</th></tr></thead>
    <tbody>
      @foreach($programBalances as $pb)
      <tr>
        <td>{{ $pb['program']->name }}</td>
        <td class="right">{{ number_format($pb['balance'],0,',','.') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>

