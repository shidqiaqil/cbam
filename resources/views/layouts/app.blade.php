<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $title ?? 'CBAM' }}</title>

    @include('layouts.partials.css')
    <style>
        :root {
            margin: 0;
        }
    </style>
    @stack('style')
    @livewireStyles
</head>

<body>

    <div class="page">
        @include('layouts.partials.navbar')

        {{ $slot }}

        @include('layouts.partials.footer')


    </div>
    @livewireScripts

    @include('layouts.partials.js')

    @stack('script')
</body>

</html>