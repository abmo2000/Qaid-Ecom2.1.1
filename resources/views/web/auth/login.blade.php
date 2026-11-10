@extends('web.layouts.main')

@section('title')
    {{ trans('auth.sign_in') }}
@endsection

@section('content')
    <x-navbar></x-navbar>

    <section class="qaid-page-background min-h-screen px-4 pb-16 pt-32 text-qaid-navy md:pb-24 md:pt-40">
        <div class="mx-auto max-w-md">
            <h1 class="mb-8 text-center text-4xl font-black uppercase tracking-wide text-qaid-navy sm:text-5xl">
                {{ trans('auth.sign_in') }}
            </h1>

            <div class="rounded-3xl border border-qaid-gold/25 bg-white/95 p-6 shadow-2xl shadow-qaid-navy/10 sm:p-10">
                @if(session('status') === 'passwords.reset')
                    <p role="status" class="mb-6 rounded-xl border border-qaid-gold/35 bg-qaid-cream px-4 py-3 text-sm text-qaid-navy">
                        {{ trans('auth.password_reset_success') }}
                    </p>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <x-web-input
                        type="email"
                        name="email"
                        :label="trans('auth.email')"
                        :placeholder="trans('auth.email-placeholder')"
                        label-class="text-qaid-navy"
                        error-class="text-red-700"
                        class="auth-input-light"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <div class="space-y-2">
                        <x-web-input
                            type="password"
                            name="password"
                            :label="trans('auth.pass')"
                            :placeholder="trans('auth.password-placeholder')"
                            label-class="text-qaid-navy"
                            toggle-class="text-slate-500 hover:text-qaid-navy"
                            error-class="text-red-700"
                            class="auth-input-light"
                            required
                            autocomplete="current-password"
                        />
                        <div class="text-end">
                            <a href="{{ route('password.request') }}"
                               class="text-sm font-bold text-qaid-gold-dark transition hover:text-qaid-navy">
                                {{ trans('auth.forgot_password') }}
                            </a>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-full bg-qaid-navy px-8 py-4 font-black uppercase tracking-wider text-qaid-gold-text shadow-xl shadow-qaid-navy/20 transition hover:-translate-y-0.5 hover:bg-qaid-navy-hover focus:outline-none focus:ring-2 focus:ring-qaid-gold focus:ring-offset-2"
                    >
                        {{ trans('auth.sign_in') }}
                    </button>
                </form>

                <p class="mt-7 text-center text-slate-600">
                    {{ trans('auth.no_account') }}
                    <a href="{{ route('register') }}" class="font-black text-qaid-gold-dark transition hover:text-qaid-navy">
                        {{ trans('auth.sign_up') }}
                    </a>
                </p>
            </div>
        </div>
    </section>
@endsection
