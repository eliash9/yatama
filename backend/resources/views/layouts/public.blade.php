<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Donasi</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
  <div class="max-w-md mx-auto pb-20">
    <header class="sticky top-0 z-10 bg-white/90 backdrop-blur border-b">
      <div class="px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
          @if(env('PUBLIC_LOGO_URL'))
            <img src="{{ env('PUBLIC_LOGO_URL') }}" alt="logo" class="w-7 h-7 rounded" />
          @else
            <div class="w-7 h-7 rounded bg-blue-600"></div>
          @endif
          <div class="font-semibold">{{ env('PUBLIC_SITE_NAME','Yatama') }}</div>
        </div>
        <div>
          @if(session('donor_id'))
            <a href="{{ route('public.donor.dashboard') }}" class="text-sm bg-gray-100 rounded px-3 py-1.5">Akun Saya</a>
          @else
            <a href="{{ route('public.donor.login') }}" class="text-sm bg-gray-100 rounded px-3 py-1.5">Masuk</a>
          @endif
        </div>
      </div>
    </header>
    <main class="p-4">
      @yield('content')
    </main>
    <nav class="fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-white border-t shadow-inner">
      <div class="grid grid-cols-3 text-sm">
        <a href="{{ route('public.donation.index') }}" class="text-center py-3 {{ request()->routeIs('public.donation.index') ? 'text-blue-600 font-medium' : 'text-gray-600' }}">Donasi</a>
        <a href="{{ route('public.donation.status') }}" class="text-center py-3 {{ request()->routeIs('public.donation.status') ? 'text-blue-600 font-medium' : 'text-gray-600' }}">Status</a>
        @if(session('donor_id'))
          <a href="{{ route('public.donor.dashboard') }}" class="text-center py-3 {{ request()->routeIs('public.donor.*') ? 'text-blue-600 font-medium' : 'text-gray-600' }}">Akun</a>
        @else
          <a href="{{ route('public.donor.login') }}" class="text-center py-3 {{ request()->routeIs('public.donor.*') ? 'text-blue-600 font-medium' : 'text-gray-600' }}">Akun</a>
        @endif
      </div>
    </nav>
  </div>
</body>
</html>
