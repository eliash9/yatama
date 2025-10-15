@if (session('status'))
  <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm">
    {{ session('status') }}
  </div>
@endif
@if ($errors->any())
  <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm">
    <ul class="list-disc pl-5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

