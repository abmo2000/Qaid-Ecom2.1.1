@extends('web.layouts.main')

@section('title')
    {{ trans('auth.reset_password') }}
@endsection

@section('content')
    <x-navbar></x-navbar>

    <section class="qaid-page-background min-h-screen px-4 pb-16 pt-32 text-qaid-navy md:pb-24 md:pt-40">
        <div class="mx-auto max-w-md">
            <h1 class="mb-8 text-center text-4xl font-black uppercase tracking-wide text-qaid-navy sm:text-5xl">
                {{ trans('auth.reset_password') }}
            </h1>

            <div class="rounded-3xl border border-qaid-gold/25 bg-white/95 p-6 shadow-2xl shadow-qaid-navy/10 sm:p-10">
                <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <x-web-input
                        type="email"
                        name="email"
                        :label="trans('auth.email')"
                        :placeholder="trans('auth.email-placeholder')"
                        :value="$request->email"
                        label-class="text-qaid-navy"
                        error-class="text-red-700"
                        class="auth-input-light"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <x-web-input
                        type="password"
                        name="password"
                        :label="trans('auth.new_password')"
                        :placeholder="trans('auth.password-placeholder')"
                        label-class="text-qaid-navy"
                        toggle-class="text-slate-500 hover:text-qaid-navy"
                        error-class="text-red-700"
                        class="auth-input-light"
                        required
                        autocomplete="new-password"
                    />

                    <x-web-input
                        type="password"
                        name="password_confirmation"
                        :label="trans('auth.confirm_password')"
                        :placeholder="trans('auth.confirm-password-placeholder')"
                        label-class="text-qaid-navy"
                        toggle-class="text-slate-500 hover:text-qaid-navy"
                        error-class="text-red-700"
                        class="auth-input-light"
                        required
                        autocomplete="new-password"
                    />

                    <button type="submit"
                            class="w-full rounded-full bg-qaid-navy px-8 py-4 font-black text-qaid-gold-text shadow-xl shadow-qaid-navy/20 transition hover:-translate-y-0.5 hover:bg-qaid-navy-hover focus:outline-none focus:ring-2 focus:ring-qaid-gold focus:ring-offset-2">
                        {{ trans('auth.reset_password') }}
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
