@if (session('status'))
    <p class="mb-4 border border-green-200 bg-green-50 px-3 py-2 text-green-800">{{ session('status') }}</p>
@endif
@if ($errors->any())
    <ul class="mb-4 list-inside list-disc border border-red-200 bg-red-50 px-3 py-2 text-red-700">
        @foreach ($errors->unique() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif
