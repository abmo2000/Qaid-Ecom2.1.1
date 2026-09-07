@extends('web.layouts.main')

@section('title', trans('wholesale.title'))

@section('content')
<x-navbar></x-navbar>

<main class="relative min-h-screen overflow-hidden bg-[#0d1b2a] pb-16 pt-[12rem] text-white sm:pt-[13rem] md:pb-24 md:pt-[14rem] lg:pt-[15rem]">
    <div class="absolute inset-0">
        <video
            autoplay
            muted
            loop
            playsinline
            preload="auto"
            poster="{{ asset('assets/images/headers/contactBackground.jfif') }}"
            class="h-full w-full object-cover brightness-75 contrast-110"
        >
            <source src="{{ asset('assets/images/headers/Hero.mp4') }}" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-[#0d1b2a]/75 bg-gradient-to-b from-[#0d1b2a]/60 via-[#0d1b2a]/75 to-[#0d1b2a]/95"></div>
    </div>

    <div class="container relative z-10 mx-auto px-4">
        <header class="mx-auto mb-10 max-w-2xl text-center md:mb-12">
            <p class="mb-3 text-xs font-bold uppercase tracking-[0.28em] text-[#d8b35a]">{{ trans('wholesale.download') }}</p>
            <h1 class="heading text-4xl font-black uppercase tracking-wide text-[#f7d879] md:text-5xl">{{ trans('wholesale.title') }}</h1>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-white/70 md:text-base">{{ trans('wholesale.intro') }}</p>
        </header>

        <div class="mx-auto max-w-3xl rounded-3xl border border-[#d8b35a]/35 bg-white/5 p-5 shadow-2xl backdrop-blur-md sm:p-8 md:p-10">
            <div class="mb-8 flex items-center gap-4 border-b border-white/10 pb-6">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#d8b35a] text-[#0d1b2a] shadow-lg shadow-[#d8b35a]/20">
                    <i class="fas fa-file-invoice-dollar text-lg" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="text-lg font-black text-white md:text-xl">{{ trans('wholesale.download') }}</h2>
                    <p class="mt-1 text-sm text-white/55">{{ trans('wholesale.intro') }}</p>
                </div>
            </div>

            <x-errors></x-errors>

            @if (session('success'))
                <div id="wholesale-success" class="mb-6 rounded-2xl border border-emerald-300/40 bg-emerald-500/15 p-4 text-sm font-semibold text-emerald-100" role="status">
                    {{ session('success') }}
                </div>
                <div id="wholesale-download-error" class="mb-6 hidden rounded-2xl border border-red-300/40 bg-red-500/15 p-4 text-sm font-semibold text-red-100" role="alert"></div>
            @endif

            <form action="{{ route('wholesale-sales.price-quote') }}" method="POST" class="space-y-7">
                @csrf

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label for="business_name" class="mb-2 block text-sm font-bold uppercase tracking-wide text-white/80">{{ trans('wholesale.business_name') }} <span class="text-[#f7d879]" aria-hidden="true">*</span></label>
                        <input id="business_name" name="business_name" value="{{ old('business_name') }}" required maxlength="255" type="text" autocomplete="organization" class="w-full rounded-xl border border-[#d8b35a]/30 bg-[#0d1b2a]/90 px-4 py-3.5 text-white transition placeholder:text-white/30 focus:border-[#d8b35a] focus:outline-none focus:ring-2 focus:ring-[#d8b35a]/30" aria-required="true">
                    </div>

                    <div>
                        <label for="phone" class="mb-2 block text-sm font-bold uppercase tracking-wide text-white/80">{{ trans('wholesale.phone') }} <span class="text-[#f7d879]" aria-hidden="true">*</span></label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" required maxlength="14" minlength="11" type="tel" inputmode="tel" pattern="(?:01[0125][0-9]{8}|(?:\+20|0020)1[0125][0-9]{8})" autocomplete="tel" class="w-full rounded-xl border border-[#d8b35a]/30 bg-[#0d1b2a]/90 px-4 py-3.5 text-white transition placeholder:text-white/30 focus:border-[#d8b35a] focus:outline-none focus:ring-2 focus:ring-[#d8b35a]/30" aria-required="true" aria-describedby="phone-error">
                        <p id="phone-error" class="mt-2 hidden text-sm font-semibold text-red-300" role="alert"></p>
                    </div>
                </div>

                <div>
                    <label for="message" class="mb-2 block text-sm font-bold uppercase tracking-wide text-white/80">{{ trans('wholesale.message') }}</label>
                    <textarea id="message" name="message" rows="4" maxlength="2000" class="w-full resize-y rounded-xl border border-[#d8b35a]/30 bg-[#0d1b2a]/90 px-4 py-3.5 text-white transition placeholder:text-white/30 focus:border-[#d8b35a] focus:outline-none focus:ring-2 focus:ring-[#d8b35a]/30" placeholder="{{ trans('wholesale.message_placeholder') }}">{{ old('message') }}</textarea>
                </div>

                <div class="rounded-2xl border border-[#d8b35a]/25 bg-[#0d1b2a]/50 p-4 sm:p-5">
                    <label class="flex items-start gap-3 text-sm leading-6 text-white/80">
                        <input name="terms" value="1" required type="checkbox" class="mt-1 h-4 w-4 shrink-0 accent-[#d8b35a]" aria-required="true">
                        <span>{{ trans('wholesale.terms') }} <a href="{{ route('terms') }}" target="_blank" rel="noopener" class="font-semibold text-[#f7d879] underline decoration-[#d8b35a]/60 underline-offset-4 transition hover:text-white">({{ trans('wholesale.terms_link') }})</a></span>
                    </label>
                </div>

                <button type="submit" class="group flex w-full items-center justify-center gap-3 rounded-xl bg-[#d8b35a] px-6 py-4 font-black uppercase tracking-wide text-[#0d1b2a] shadow-lg shadow-[#d8b35a]/10 transition duration-300 hover:bg-[#f7d879] hover:shadow-[#d8b35a]/30 focus:outline-none focus:ring-2 focus:ring-[#f7d879] focus:ring-offset-2 focus:ring-offset-[#0d1b2a]">
                    <i class="fas fa-download transition-transform duration-300 group-hover:translate-y-0.5" aria-hidden="true"></i>
                    <span>{{ trans('wholesale.download') }}</span>
                </button>
            </form>
        </div>
    </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const phone = document.getElementById('phone');
        const phoneError = document.getElementById('phone-error');
        const phoneMessage = @json(trans('wholesale.phone_invalid'));
        const phonePattern = /^(?:01[0125][0-9]{8}|(?:\+20|0020)1[0125][0-9]{8})$/;

        if (!phone || !phoneError) {
            return;
        }

        const validatePhone = () => {
            const valid = phonePattern.test(phone.value);
            const showError = phone.value.length > 0 && !valid;

            phone.setCustomValidity(showError ? phoneMessage : '');
            phoneError.textContent = showError ? phoneMessage : '';
            phoneError.classList.toggle('hidden', !showError);
            phone.classList.toggle('border-red-400', showError);

            return valid;
        };

        phone.addEventListener('input', validatePhone);
        phone.addEventListener('blur', validatePhone);
        phone.form?.addEventListener('submit', (event) => {
            if (!validatePhone()) {
                event.preventDefault();
                phone.reportValidity();
                phone.focus();
            }
        });
    });
</script>
@if (session('wholesale_download_url'))
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const success = document.getElementById('wholesale-success');
            const error = document.getElementById('wholesale-download-error');

            try {
                const response = await fetch(@json(session('wholesale_download_url')), {
                    headers: { 'Accept': 'application/pdf' },
                });

                if (!response.ok) {
                    throw new Error('download_failed');
                }

                const blob = await response.blob();
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'price-quote.pdf';
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(link.href);
            } catch (downloadError) {
                success?.remove();
                if (error) {
                    error.textContent = @json(trans('wholesale.download_failed'));
                    error.classList.remove('hidden');
                }
            }
        });
    </script>
@endif
@endsection