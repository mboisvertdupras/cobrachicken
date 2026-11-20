@if (session('status'))
    <p class="notice">{{ session('status') }}</p>
@endif
@if ($errors->any())
    <ul class="errors">
        @foreach ($errors->unique() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif
