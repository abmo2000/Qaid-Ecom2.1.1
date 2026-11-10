@extends('web.layouts.main')


@section('title')
  Register
@endsection

@section('content')
 <x-navbar></x-navbar>

 <section class="qaid-page-background min-h-screen px-4 pb-16 pt-32 text-qaid-navy md:pb-20 md:pt-40">
   <div class="container mx-auto px-4">
    <h1 class="mb-10 text-center text-4xl font-black uppercase tracking-wide text-qaid-navy sm:text-5xl">{{ trans('auth.register') }}</h1>

    <div class="max-w-3xl mx-auto">
      <!-- Registration Card -->
      <div class="rounded-3xl border border-qaid-gold/25 bg-white/95 p-6 shadow-2xl shadow-qaid-navy/10 sm:p-10">

        <!-- Display Validation Errors -->
        <x-errors tone="light"></x-errors>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
          @csrf

          <!-- Name + Email side by side -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <x-web-input
              name="name"
              :label="trans('auth.name') ?? 'Name'"
              :placeholder="trans('auth.name-placeholder')"
              label-class="text-qaid-navy"
              error-class="text-red-700"
              class="auth-input-light"
              required
              autofocus></x-web-input>

            <x-web-input
              type="email"
              name="email"
              :label="trans('auth.email') ?? 'Email'"
              :placeholder="trans('auth.email-placeholder')"
              label-class="text-qaid-navy"
              error-class="text-red-700"
              class="auth-input-light"
              required></x-web-input>
          </div>

          <!-- Phone + City side by side -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label for="phone" class="mb-2 block text-sm font-semibold text-qaid-navy">{{ trans('auth.phone') }}</label>
              <input
                type="tel"
                id="phone"
                name="phone"
                value="{{ old('phone') }}"
                class="auth-input-light w-full rounded-xl px-4 py-3 transition"
                placeholder="{{ trans('auth.phone-placeholder') }}"
                required
                autocomplete="tel"
              />
              <input type="hidden" name="full_phone" id="full_phone" value="{{ old('full_phone') }}" />
            </div>

            <x-web-select
              name="city_id"
              :label="trans('auth.city') ?? 'City'"
              :placeholder="trans('auth.city-placeholder') ?? 'Select your city'"
              :options="getCities()"
              label-class="text-qaid-navy"
              error-class="text-red-700"
              class="auth-input-light"
              required
            />
          </div>
          <p class="-mt-3 text-xs text-slate-500">{{ trans('auth.phone_without_country_code') }}</p>

          <!-- Address -->
          <x-web-textarea
            name="address"
            :label="trans('auth.address') ?? 'Address'"
            :placeholder="trans('auth.address-placeholder')"
            label-class="text-qaid-navy"
            error-class="text-red-700"
            class="auth-input-light"
            rows="2"
          ></x-web-textarea>

          <!-- Password + Confirm side by side -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <x-web-input
              type="password"
              name="password"
              :label="trans('auth.pass') ?? 'Password'"
              :placeholder="trans('auth.password-placeholder')"
              label-class="text-qaid-navy"
              toggle-class="text-slate-500 hover:text-qaid-navy"
              error-class="text-red-700"
              class="auth-input-light"
              required
            />

            <x-web-input
              type="password"
              name="password_confirmation"
              :label="trans('auth.confirm_password') ?? 'Confirm Password'"
              :placeholder="trans('auth.confirm-password-placeholder')"
              label-class="text-qaid-navy"
              toggle-class="text-slate-500 hover:text-qaid-navy"
              error-class="text-red-700"
              class="auth-input-light"
              required
            />
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="w-full cursor-pointer rounded-full bg-qaid-navy px-10 py-4 font-black uppercase tracking-wider text-qaid-gold-text shadow-xl shadow-qaid-navy/20 transition hover:-translate-y-0.5 hover:bg-qaid-navy-hover"
          >
            {{ trans('auth.register') ?? 'Create Account' }}
          </button>
        </form>

        <!-- Login Link -->
        <div class="mt-6 text-center">
          <p class="text-sm text-slate-600">
            {{ trans('auth.have-account') }}
            <button onclick="openSignInModal()" class="font-semibold text-qaid-gold-dark transition hover:text-qaid-navy">
              {{ trans('auth.login') ?? 'Login here' }}
            </button>
          </p>
        </div>

        <!-- Social Login -->
        <div class="mt-8">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-[#d8b35a]/25"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="bg-white px-4 text-slate-500">{{ trans('auth.continue-with') }}</span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-1 gap-4">
            <a href="{{ route('google.login') }}" class="flex cursor-pointer items-center justify-center rounded-full border border-qaid-gold/40 bg-white px-4 py-3 text-qaid-navy transition duration-200 hover:border-qaid-gold hover:bg-qaid-cream">
              <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
              </svg>
              {{ trans('auth.google') }}
            </a>
          </div>
        </div>
      </div>
    </div>

   </div>
 </section>
 @include('web.pages.partials.login-modal');

 <script>
   function openSignInModal(){
        document.getElementById('signinModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
  }
 </script>

<!-- Initialize intl-tel-input for dynamic country code selection -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.1.0/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.1.0/build/js/intlTelInput.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var phoneInput = document.getElementById('phone');
    var fullPhoneInput = document.getElementById('full_phone');
    if (!phoneInput) return;

    var iti = window.intlTelInput(phoneInput, {
      initialCountry: 'eg',
      separateDialCode: true,
      utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.1.0/build/js/utils.js'
    });

    if (phoneInput.value) {
      try {
        iti.setNumber(phoneInput.value);
      } catch (error) {
        // ignore invalid initial value
      }
    }

    var form = phoneInput.closest('form');
    if (!form) return;

    var phoneError = document.createElement('div');
    phoneError.id = 'phoneError';
    phoneError.className = 'text-red-400 text-sm mb-3 hidden';
    phoneInput.parentNode.insertBefore(phoneError, phoneInput.nextSibling);

    phoneInput.addEventListener('input', function () {
      phoneError.classList.add('hidden');
      phoneInput.classList.remove('border-red-400');
    });

    form.addEventListener('submit', function (e) {
      var selectedDialCode = iti.getSelectedCountryData().dialCode || '';
      var normalized = phoneInput.value.replace(/\D/g, '');

      if (selectedDialCode && normalized.startsWith(selectedDialCode)) {
        normalized = normalized.slice(selectedDialCode.length);
      }

      phoneInput.value = normalized;
      phoneError.classList.add('hidden');
      phoneInput.classList.remove('border-red-400');

      try {
        var full = iti.getNumber();
        if (full) {
          fullPhoneInput.value = full;
        }
      } catch (err) {
        fullPhoneInput.value = phoneInput.value;
      }
    });
  });
</script>
@endsection
