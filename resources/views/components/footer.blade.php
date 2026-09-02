@php
    $businessInfo = getBuisnessSettings('buisness-info');
    $telegramRaw = $businessInfo?->telegram_link ?? '';
    $telegramLink = $telegramRaw ? (
        str_starts_with($telegramRaw, 'http')
            ? $telegramRaw
            : (str_starts_with($telegramRaw, '@')
                ? 'https://t.me/' . ltrim($telegramRaw, '@')
                : 'https://t.me/' . ltrim($telegramRaw, '/'))
    ) : '#';
@endphp

<!-- footer -->
<footer class="bg-[#0d1b2a] text-white">
    <div class="container mx-auto px-4 py-10">
        <div class="grid gap-8 border-b border-[#d8b35a]/20 pb-8 md:grid-cols-[auto_1fr_auto] md:items-center">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="mx-auto flex items-center gap-4 md:mx-0">
                <img
                    class="h-16 w-16 rounded-2xl border border-[#d8b35a]/35 object-cover shadow-[0_18px_45px_rgba(0,0,0,.25)]"
                    src="{{ asset('assets/images/logos/logo.jpg') }}"
                    alt="Qaid-eg logo"
                >
                <div class="md:hidden">
                    <p class="text-lg font-black uppercase tracking-[0.16em] text-[#f7d879]">Qaid-eg</p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-[0.18em] text-white/55">Premium Tech Store</p>
                </div>
            </a>

            <!-- Navigation -->
            <div class="flex flex-col items-center gap-5 text-center">
                <div class="hidden md:block">
                    <h3 class="text-2xl font-black uppercase tracking-[0.18em] text-[#f7d879]">Qaid-eg</h3>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-[0.22em] text-white/55">Premium Tech Store</p>
                </div>

                <nav class="flex flex-wrap items-center justify-center gap-x-6 gap-y-3">
                    <a href="{{ route('home') }}" class="text-sm font-bold uppercase tracking-[0.12em] text-white/70 transition hover:text-[#f7d879]">{{ trans('navbar.home') }}</a>
                    <a href="{{ route('shop') }}" class="text-sm font-bold uppercase tracking-[0.12em] text-white/70 transition hover:text-[#f7d879]">{{ trans('navbar.shop') }}</a>
                    <a href="{{ route('contact') }}" class="text-sm font-bold uppercase tracking-[0.12em] text-white/70 transition hover:text-[#f7d879]">{{ trans('navbar.contact') }}</a>
                    <a href="{{ route('terms') }}" class="text-sm font-bold uppercase tracking-[0.12em] text-white/70 transition hover:text-[#f7d879]">{{ trans('navbar.terms') ?? 'Terms' }}</a>
                </nav>
            </div>

            <!-- Social Media Icons -->
            <div class="flex items-center justify-center gap-3 md:justify-end">
                <a
                    href="{{ getBuisnessSettings('buisness-info')?->facebook_link }}"
                    class="flex h-11 w-11 items-center justify-center rounded-full border border-[#d8b35a]/30 bg-white/5 text-[#f7d879] transition hover:-translate-y-1 hover:border-[#d8b35a] hover:bg-[#f7d879] hover:text-[#0d1b2a]"
                    target="_blank"
                    rel="noopener"
                    aria-label="Facebook"
                >
                    <i class="fab fa-facebook text-lg"></i>
                </a>

                <a
                    href="{{ getBuisnessSettings('buisness-info')?->instagram_link }}"
                    class="flex h-11 w-11 items-center justify-center rounded-full border border-[#d8b35a]/30 bg-white/5 text-[#f7d879] transition hover:-translate-y-1 hover:border-[#d8b35a] hover:bg-[#f7d879] hover:text-[#0d1b2a]"
                    target="_blank"
                    rel="noopener"
                    aria-label="Instagram"
                >
                    <i class="fab fa-instagram text-lg"></i>
                </a>

                <a href={{ "https://wa.me/+20" . getBuisnessSettings('buisness-info')?->whatsapp_number }}
                    target="_blank"
                    rel="noopener"
                    class="flex h-11 w-11 items-center justify-center rounded-full border border-[#d8b35a]/30 bg-white/5 text-[#f7d879] transition hover:-translate-y-1 hover:border-[#d8b35a] hover:bg-[#f7d879] hover:text-[#0d1b2a]"
                    aria-label="WhatsApp"
                >
                    <i class="fab fa-whatsapp text-lg"></i>
                </a>

                @if(!empty($telegramRaw))
                    <a
                        href="{{ $telegramLink }}"
                        target="_blank"
                        rel="noopener"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-[#d8b35a]/30 bg-white/5 text-[#f7d879] transition hover:-translate-y-1 hover:border-[#d8b35a] hover:bg-[#f7d879] hover:text-[#0d1b2a]"
                        aria-label="Telegram"
                    >
                        <i class="fab fa-telegram-plane text-lg"></i>
                    </a>
                @endif
            </div>
        </div>

        <!-- Bottom Section - Copyright & Credits -->
        <div class="flex flex-col items-center justify-between gap-4 pt-6 text-center text-sm font-semibold text-white/45 md:flex-row">
            <p>
                Copyright © <span id="currentYear">2026</span> Qaid-eg. All rights reserved.
            </p>
            <p>
                Developed by <a href="#" class="font-black text-[#f7d879] transition hover:text-white">Qaid-eg</a>
            </p>
        </div>
    </div>
</footer>
