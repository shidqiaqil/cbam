<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Sign In' }}</title>
    @include('layouts.partials.css')
    @stack('styles')
    @livewireStyles
</head>

<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="w-100" style="max-width:420px">
            {{ $slot ?? $content ?? '' }}
        </div>
    </div>

    @livewireScripts
    @include('layouts.partials.js')
    @stack('scripts')
</body>

</html>