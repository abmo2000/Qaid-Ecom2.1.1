@extends('web.layouts.main')

@section('title')
    {{ trans('auth.forgot_password_title') }}
@endsection

@section('content')
    <x-navbar></x-navbar>

    <section class="qaid-page-background min-h-screen px-4 pb-16 pt-32 text-qaid-navy md:pb-24 md:pt-40">
        <div class="mx-auto max-w-md">
            <h1 class="mb-8 text-center text-4xl font-black uppercase tracking-wide text-qaid-navy sm:text-5xl">
                {{ trans('auth.forgot_password_title') }}
            </h1>

            <div class="rounded-3xl border border-qaid-gold/25 bg-white/95 p-6 shadow-2xl shadow-qaid-navy/10 sm:p-10">
                <p class="mb-6 text-center text-slate-600">
                    {{ trans('auth.reset_password_instructions') }}
                </p>

                @if(session('status'))
                    <p role="status" class="mb-6 rounded-xl border border-qaid-gold/35 bg-qaid-cream px-4 py-3 text-sm text-qaid-navy">
                        {{ trans('auth.reset_link_sent') }}
                    </p>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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

                    <button type="submit"
                            class="w-full rounded-full bg-qaid-navy px-8 py-4 font-black text-qaid-gold-text shadow-xl shadow-qaid-navy/20 transition hover:-translate-y-0.5 hover:bg-qaid-navy-hover focus:outline-none focus:ring-2 focus:ring-qaid-gold focus:ring-offset-2">
                        {{ trans('auth.send_reset_link') }}
                    </button>
                </form>

                <p class="mt-7 text-center">
                    <a href="{{ route('login') }}" class="font-black text-qaid-gold-dark transition hover:text-qaid-navy">
                        {{ trans('auth.back_to_login') }}
                    </a>
                </p>
            </div>
        </div>
    </section>
@endsection
