<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Yatama Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <link rel="icon" href="data:">
</head>
<body class="bg-gray-50 text-gray-900">
  <div class="min-h-screen flex">
    <aside class="w-64 bg-white border-r hidden md:flex md:flex-col">
      <div class="px-4 py-4 border-b">
        <a href="/dashboard" class="text-lg font-semibold">Yatama</a>
        <div class="text-xs text-gray-500">Sistem Manajemen Dana</div>
      </div>
      @auth
      <nav class="flex-1 overflow-y-auto px-3 py-4 text-sm">
        @role('admin')
        <div class="mb-4">
          <div class="px-3 text-gray-500 uppercase tracking-wide text-xs mb-2">Admin</div>
          <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 font-medium' : '' }}">Pengguna</a>
          <a href="{{ route('admin.roles.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('admin.roles.*') ? 'bg-gray-100 font-medium' : '' }}">Peran & Permissions</a>
        </div>
        @endrole
        <div class="mb-4">
          <div class="px-3 text-gray-500 uppercase tracking-wide text-xs mb-2">Umum</div>
          <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-gray-100 font-medium' : '' }}">Dashboard</a>
        </div>
        <div class="mb-4">
          <div class="px-3 text-gray-500 uppercase tracking-wide text-xs mb-2">Master Data</div>
          <a href="{{ route('master.donors.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('master.donors.*') ? 'bg-gray-100 font-medium' : '' }}">Donatur</a>
          <a href="{{ route('master.beneficiaries.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('master.beneficiaries.*') ? 'bg-gray-100 font-medium' : '' }}">Penerima Manfaat</a>
          <a href="{{ route('master.volunteers.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('master.volunteers.*') ? 'bg-gray-100 font-medium' : '' }}">Relawan</a>
          <a href="{{ route('master.programs.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('master.programs.*') ? 'bg-gray-100 font-medium' : '' }}">Program/Kampanye</a>
          <a href="{{ route('master.accounts.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('master.accounts.*') ? 'bg-gray-100 font-medium' : '' }}">Akun Kas/Bank</a>
        </div>
        <div class="mb-4">
          <div class="px-3 text-gray-500 uppercase tracking-wide text-xs mb-2">Keuangan</div>
          <a href="{{ route('finance.incomes.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.incomes.*') ? 'bg-gray-100 font-medium' : '' }}">Penerimaan Dana</a>
          <a href="{{ route('finance.transactions.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.transactions.*') ? 'bg-gray-100 font-medium' : '' }}">Transaksi Kas/Bank</a>
          <a href="{{ route('finance.mutations.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.mutations.*') ? 'bg-gray-100 font-medium' : '' }}">Mutasi Bank</a>
          <a href="{{ route('finance.cashbook') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.cashbook') ? 'bg-gray-100 font-medium' : '' }}">Buku Kas</a>
          <div class="px-3 text-gray-400 text-xs mt-2">Pelaporan</div>
          <a href="{{ route('finance.reports.balances') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.reports.balances') ? 'bg-gray-100 font-medium' : '' }}">Saldo Akun & Program</a>
          <a href="{{ route('finance.reports.balance_sheet') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.reports.balance_sheet') ? 'bg-gray-100 font-medium' : '' }}">Neraca</a>
          <a href="{{ route('finance.reports.incomes') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.reports.incomes') ? 'bg-gray-100 font-medium' : '' }}">Penerimaan</a>
          <a href="{{ route('finance.reports.disbursements') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.reports.disbursements') ? 'bg-gray-100 font-medium' : '' }}">Penyaluran</a>
          <a href="{{ route('finance.reports.cashflow') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.reports.cashflow') ? 'bg-gray-100 font-medium' : '' }}">Arus Kas</a>
          <a href="{{ route('finance.reports.activity') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.reports.activity') ? 'bg-gray-100 font-medium' : '' }}">Aktivitas</a>
          <a href="{{ route('finance.reports.funds') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.reports.funds') ? 'bg-gray-100 font-medium' : '' }}">Dana Terikat vs Umum</a>
          <a href="{{ route('finance.reports.campaigns') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.reports.campaigns') ? 'bg-gray-100 font-medium' : '' }}">Realisasi Kampanye</a>
          <a href="{{ route('finance.reports.operational_ratio') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.reports.operational_ratio') ? 'bg-gray-100 font-medium' : '' }}">Rasio Operasional</a>
          <a href="{{ route('finance.disbursements.index') }}" class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('finance.disbursements.*') ? 'bg-gray-100 font-medium' : '' }}">Penyaluran</a>
        </div>
      </nav>
      <div class="px-3 py-4 border-t text-xs text-gray-600">
        <form method="POST" action="{{ route('logout') }}" class="flex items-center justify-between">
          @csrf
          <span>Masuk sebagai <span class="font-medium">{{ auth()->user()->name }}</span></span>
          <button class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">Keluar</button>
        </form>
      </div>
      @else
      <div class="p-4"><a href="{{ route('login') }}" class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Masuk</a></div>
      @endauth
    </aside>
    <div class="flex-1 flex flex-col min-w-0">
      <header class="bg-white border-b md:hidden">
        <div class="px-4 py-3 flex items-center justify-between">
          <a href="/dashboard" class="font-semibold">Yatama</a>
          @auth
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="px-3 py-1 bg-gray-100 rounded">Keluar</button>
          </form>
          @else
          <a href="{{ route('login') }}" class="px-3 py-1 bg-blue-600 text-white rounded">Masuk</a>
          @endauth
        </div>
      </header>
      <main class="px-4 md:px-8 py-6">
        {{ $slot ?? '' }}
        @yield('content')
      </main>
    </div>
  </div>
</body>
</html>
