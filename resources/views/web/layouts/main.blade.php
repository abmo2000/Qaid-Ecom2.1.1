<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }} | @yield('title')</title>

        @stack('meta')
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Kurale&display=swap" rel="stylesheet">

          <meta name="csrf-token" content="{{ csrf_token() }}">

        <script>
            (() => {
                try {
                    const storedTheme = localStorage.getItem('qaid_theme');
                    const theme = storedTheme === 'light' ? 'light' : 'dark';

                    document.documentElement.classList.toggle('theme-light', theme === 'light');
                    document.documentElement.dataset.theme = theme;
                } catch (_) {
                    document.documentElement.classList.remove('theme-light');
                    document.documentElement.dataset.theme = 'dark';
                }
            })();
        </script>

        <!-- Styles / Scripts -->
         @vite(['resources/css/app.css', 'resources/js/app.js'])
           
    
         @stack('styles_bottom')
    </head>
    <body class="app-body">
        @yield('content')
        <x-flash-message></x-flash-message>
        <x-footer></x-footer>
        @auth
            @if(auth()->user()->isCustomer())
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        fetch("{{ route('customer.traffic.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            },
                            body: JSON.stringify({
                                url: window.location.href,
                                path: window.location.pathname + window.location.search,
                                referer: document.referrer || null,
                                user_agent: navigator.userAgent || null,
                            }),
                        }).catch(() => {
                            // Ignore tracking failures silently.
                        });
                    });
                </script>
            @endif
        @endauth
        @stack('scripts_bottom')
    </body>
</html>
