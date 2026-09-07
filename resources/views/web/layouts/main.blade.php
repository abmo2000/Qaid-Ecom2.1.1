<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
>
    <head>
        @php
            $seoPageByRoute = [
                'home' => 'home',
                'shop' => 'shop',
                'contact' => 'contact',
                'wholesale-sales' => 'wholesale-sales',
                'terms' => 'terms',
                'routines.index' => 'routines',
                'routines.show' => 'routines',
                'packages.show' => 'packages',
            ];
            $seo = isset($seoData)
                ? $seoData
                : (isset($seoPageByRoute[request()->route()?->getName()])
                    ? getPageSeo($seoPageByRoute[request()->route()->getName()])
                    : null);
            $pageTitle = $seo['meta_title'] ?? trim($__env->yieldContent('title')) ?: config('app.name');
        @endphp
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $pageTitle }}</title>

        @if ($seo)
            <meta name="description" content="{{ $seo['meta_description'] ?? '' }}">
            @if (!empty($seo['meta_keywords']))
                <meta name="keywords" content="{{ $seo['meta_keywords'] }}">
            @endif
            <meta name="robots" content="index, follow">
            <meta property="og:type" content="website">
            <meta property="og:title" content="{{ $pageTitle }}">
            <meta property="og:description" content="{{ $seo['meta_description'] ?? '' }}">
            <meta property="og:url" content="{{ request()->url() }}">
            <meta name="twitter:card" content="summary">
            <meta name="twitter:title" content="{{ $pageTitle }}">
            <meta name="twitter:description" content="{{ $seo['meta_description'] ?? '' }}">
        @endif

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
        @php
            $businessInfo = getBuisnessSettings('buisness-info');

            $whatsappRaw = $businessInfo?->whatsapp_number ?: $businessInfo?->mobile_number ?: '';
            $whatsappDigits = preg_replace('/\D+/', '', (string) $whatsappRaw);
            $whatsappDigits = preg_replace('/^0+/', '', $whatsappDigits);
            $whatsappLink = $whatsappDigits
                ? 'https://wa.me/' . (str_starts_with($whatsappDigits, '20') ? '+'.$whatsappDigits : '+20'.$whatsappDigits)
                : 'https://wa.me/';

            $telegramRaw = $businessInfo?->telegram_link ?? '';
            $telegramLink = $telegramRaw ? (
                str_starts_with($telegramRaw, 'http')
                    ? $telegramRaw
                    : (str_starts_with($telegramRaw, '@')
                        ? 'https://t.me/' . ltrim($telegramRaw, '@')
                        : 'https://t.me/' . ltrim($telegramRaw, '/'))
            ) : 'https://t.me/';
        @endphp

        @yield('content')

        <div class="fixed bottom-5 right-5 z-[9999] flex flex-col items-center gap-[10px]" style="position: fixed; right: 20px; bottom: 20px; z-index: 9999; display: flex; flex-direction: column; align-items: center; gap: 10px;">
            <a
                href="{{ $whatsappLink }}"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Chat on WhatsApp"
                class="whatsapp-float flex h-16 w-16 items-center justify-center rounded-full bg-[#25D366] text-white shadow-[0_12px_30px_rgba(37,211,102,0.5)] transition-all duration-300 hover:scale-110 hover:shadow-[0_18px_36px_rgba(37,211,102,0.7)]"
                title="Chat on WhatsApp"
                style="display: flex; flex-direction: column;"
            >
                <i class="fab fa-whatsapp text-3xl"></i>
            </a>

            @if(!empty($telegramRaw))
                <a
                    href="{{ $telegramLink }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Chat on Telegram"
                    class="telegram-float flex h-16 w-16 items-center justify-center rounded-full bg-[#229ED9] text-white shadow-[0_12px_30px_rgba(34,158,217,0.45)] transition-all duration-300 hover:scale-110 hover:shadow-[0_18px_36px_rgba(34,158,217,0.7)]"
                    title="Chat on Telegram"
                    style="display: flex; flex-direction: column;"
                >
                    <i class="fab fa-telegram-plane text-3xl"></i>
                </a>
            @endif
        </div>

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
