@extends('web.layouts.main')

@section('title')
  Login
@endsection

@section('content')
 <x-navbar></x-navbar>

 <section class="py-16 md:py-24 bg-black min-h-screen">
   <div class="container mx-auto px-4">
    <h2 class="text-white text-center text-4xl md:text-5xl font-bold mb-16">{{ trans('auth.sign_in') }}</h2>

    <div class="max-w-md mx-auto">
      <div class="bg-gray-800 bg-opacity-95 backdrop-blur-md rounded-3xl shadow-2xl border border-gray-700 p-8 md:p-12">
        <x-errors />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
          @csrf

          <x-web-input
            type="email"
            name="email"
            :label="trans('auth.email') ?? 'Email address'"
            :placeholder="trans('auth.email-placeholder')"
            required
            autofocus
          />

          <x-web-input
            type="password"
            name="password"
            :label="trans('auth.pass') ?? 'Password'"
            :placeholder="trans('auth.password-placeholder')"
            required
          />

          <button
            type="submit"
            class="bg-orange-500 w-full cursor-pointer hover:bg-orange-600 text-white font-bold py-4 px-10 rounded-xl shadow-lg hover:shadow-orange-500/50 transition-all duration-300"
          >
            {{ trans('auth.sign_in') ?? 'Sign in' }}
          </button>
        </form>

        <div class="mt-6 text-center text-gray-400">
          <p>
            {{ trans('auth.no_account') ?? 'Need an account?' }}
            <a href="{{ route('register') }}" class="text-white font-semibold hover:text-orange-300">{{ trans('auth.sign_up') ?? 'Register' }}</a>
          </p>
          <p class="mt-4 text-sm text-gray-500">
            {{ trans('auth.admin_login_hint') ?? 'If you are an admin, use the admin panel login.' }}
            <a href="/admin/login" class="text-orange-400 hover:text-orange-300">/admin/login</a>
          </p>
        </div>
      </div>
    </div>
   </div>
 </section>
@endsection 