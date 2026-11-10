@extends('web.layouts.main')

@section('title', trim($__env->yieldContent('account-title')) . ' | ' . config('app.name'))

@section('content')
    <x-navbar />

    <main class="qaid-page-offset qaid-page-background min-h-screen pb-16 text-[#0d1b2a]">
        <div class="container mx-auto max-w-5xl px-4">
            <header class="mb-8 text-start">
                <p class="text-xs font-black uppercase tracking-[0.32em] text-[#9b762f]">
                    {{ __('profile.eyebrow') }}
                </p>
                <h1 class="mt-2 font-sans text-3xl font-bold text-[#0d1b2a] sm:text-4xl lg:text-5xl">
                    @yield('account-title')
                </h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                    @yield('account-description')
                </p>
                <div class="mt-4 h-px w-14 bg-[#b8913f]"></div>
            </header>

            <div class="grid items-start gap-6 lg:grid-cols-[17rem_minmax(0,1fr)] lg:gap-8">
                @include('web.account.partials.navigation', [
                    'active' => trim($__env->yieldContent('account-active')),
                    'user' => auth()->user(),
                ])

                <div class="min-w-0 space-y-6">
                    @yield('account-content')
                </div>
            </div>
        </div>
    </main>
@endsection
