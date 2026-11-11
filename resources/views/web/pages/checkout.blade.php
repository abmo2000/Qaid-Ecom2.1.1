@extends('web.layouts.main')

@section('title', __('checkout.checkout'))

@section('content')
<x-navbar></x-navbar>

@php
    $vacationSettings = getVacationSettings();
    $defaultCityId = auth()->user()?->city_id ?? '';
    if ($defaultCityId) {
        $savedCity = \App\Models\City::find($defaultCityId);
        if ($savedCity) {
            $latestCity = \App\Models\City::query()
                ->whereHas('translations', function ($query) use ($savedCity) {
                    $query->where('name', $savedCity->translations->first()?->name ?? '');
                })
                ->orderByDesc('id')
                ->first();

            if ($latestCity) {
                $defaultCityId = $latestCity->id;
            }
        }
    }

    $checkoutItems = $items->filter(fn ($item) => isset($item['name']));
@endphp

<section
    class="qaid-page-background min-h-screen px-4 pb-36 text-qaid-navy qaid-page-offset sm:pb-40 lg:pb-16"
    x-data="checkoutForm(@js($total), @js([
        'name' => auth()->user()?->name ?? '',
        'email' => auth()->user()?->email ?? '',
        'phone' => auth()->user()?->phone ?? '',
        'address' => auth()->user()?->address ?? '',
        'insta_account' => auth()->user()?->insta_account ?? '',
        'city_id' => $defaultCityId,
        'instapay' => $buisnessSettings->instapay_account ?? '',
        'isFirstOrder' => (bool) $isFirstOrder,
        'validation' => [
            'name_required' => __('checkout.validation.name_required'),
            'name_min' => __('checkout.validation.name_min'),
            'email_required' => __('checkout.validation.email_required'),
            'email_invalid' => __('checkout.validation.email_invalid'),
            'phone_required' => __('checkout.validation.phone_required'),
            'address_required' => __('checkout.validation.address_required'),
            'address_min' => __('checkout.validation.address_min'),
            'city_required' => __('checkout.validation.city_required'),
            'delivery_option_required' => __('checkout.validation.delivery_option_required'),
            'payment_method_required' => __('checkout.validation.payment_method_required'),
            'payment_proof_required' => __('checkout.validation.payment_proof_required'),
            'coupon_required' => __('checkout.validation.coupon_required'),
            'coupon_invalid' => __('checkout.validation.coupon_invalid'),
            'network_error' => __('checkout.validation.network_error'),
            'order_error' => __('checkout.validation.order_error'),
        ],
    ]), @js((bool) $orderSettings?->has_delivery_option), @js((bool) config('checkout.coupons_enabled', false)))"
>
    <div class="mx-auto max-w-5xl">
        <header class="mb-8 sm:mb-10">
            <p class="mb-2 text-center font-sans text-xs font-bold uppercase tracking-[0.2em] text-qaid-gold-dark">
                {{ __('checkout.secure_checkout') }}
            </p>
            <h1 class="text-center font-sans text-4xl font-bold text-qaid-navy sm:text-5xl">
                {{ __('checkout.checkout') }}
            </h1>
            <p class="mx-auto mt-3 max-w-xl text-center text-sm leading-6 text-slate-600 sm:text-base">
                {{ __('checkout.checkout_subtitle') }}
            </p>
            <div class="mx-auto mt-5 h-1 w-14 rounded-full bg-qaid-gold"></div>
        </header>

        <nav class="mx-auto mb-7 max-w-2xl" aria-label="{{ __('checkout.checkout_steps') }}">
            <ol class="grid grid-cols-3 items-center">
                <li class="flex items-center justify-start">
                    <a href="{{ route('cart') }}" class="flex min-h-11 items-center gap-2 text-start text-xs font-semibold text-slate-500 transition hover:text-qaid-navy sm:text-sm">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-qaid-gold/50 bg-white text-qaid-gold-dark">1</span>
                        <span>{{ __('checkout.step_cart') }}</span>
                    </a>
                </li>
                <li class="flex items-center justify-center" aria-current="step">
                    <span class="flex min-h-11 items-center gap-2 text-center text-xs font-bold text-qaid-navy sm:text-sm">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-qaid-gold bg-qaid-navy text-qaid-gold-text shadow-sm">2</span>
                        <span>{{ __('checkout.step_details') }}</span>
                    </span>
                </li>
                <li class="flex items-center justify-end">
                    <span class="flex min-h-11 items-center gap-2 text-end text-xs font-semibold text-slate-500 sm:text-sm">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-qaid-gold/50 bg-white text-qaid-gold-dark">3</span>
                        <span>{{ __('checkout.step_confirmation') }}</span>
                    </span>
                </li>
            </ol>
        </nav>

        @guest
            <aside class="mb-6 rounded-2xl border border-qaid-gold/25 bg-white/90 p-5 shadow-lg shadow-qaid-navy/5 sm:flex sm:items-center sm:justify-between sm:gap-6 sm:p-6">
                <div>
                    <h2 class="font-sans text-xl font-bold text-qaid-navy">{{ __('checkout.guest_options') }}</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ __('checkout.guest_options_description') }}</p>
                </div>
                <div class="mt-4 flex flex-wrap gap-2 sm:mt-0">
                    <a href="#checkout-form" class="inline-flex min-h-11 items-center justify-center rounded-full border border-qaid-gold/40 px-4 text-sm font-bold text-qaid-navy transition hover:bg-qaid-cream">
                        {{ __('checkout.continue_as_guest') }}
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-full border border-qaid-gold/40 px-4 text-sm font-bold text-qaid-navy transition hover:bg-qaid-cream">
                        {{ __('checkout.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center rounded-full bg-qaid-navy px-4 text-sm font-bold text-qaid-gold-text transition hover:bg-qaid-navy-hover">
                        {{ __('checkout.register') }}
                    </a>
                </div>
            </aside>
        @endguest

        <div class="mb-5 lg:hidden">
            <details class="overflow-hidden rounded-2xl border border-qaid-gold/25 bg-white shadow-lg shadow-qaid-navy/10">
                <summary class="flex min-h-14 cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 [&::-webkit-details-marker]:hidden">
                    <span class="font-sans font-bold text-qaid-navy">{{ __('checkout.order_summary') }}</span>
                    <span class="flex items-center gap-3">
                        <span class="font-sans text-sm font-bold text-qaid-navy" x-text="'EGP ' + calculateTotal().toFixed(2)"></span>
                        <i class="fas fa-chevron-down text-xs text-qaid-gold-dark" aria-hidden="true"></i>
                    </span>
                </summary>
                <div class="border-t border-qaid-gold/20 px-4 py-4">
                    <div class="space-y-3">
                        @forelse($checkoutItems as $item)
                            @php($checkoutImage = filled($item['image'] ?? null) ? $item['image'] : data_get($item, 'images.0.image'))
                            <div class="flex items-center gap-3">
                                <div class="checkout-item-image flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-qaid-gold/20 bg-white text-slate-400">
                                    @if($imageUrl = storage_image_url($checkoutImage))
                                        <img src="{{ $imageUrl }}" alt="" class="h-full w-full object-contain p-1" onerror="this.hidden = true; this.nextElementSibling.hidden = false">
                                        <span hidden class="flex h-full w-full items-center justify-center bg-slate-50">
                                            <i class="fas fa-image" aria-hidden="true"></i>
                                        </span>
                                    @else
                                        <span class="flex h-full w-full items-center justify-center bg-slate-50">
                                            <i class="fas fa-image" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1 text-start">
                                    <p class="truncate text-sm font-semibold text-qaid-navy">{{ $item['name'] }}</p>
                                    <p class="text-xs text-slate-500">{{ __('checkout.quantity') }}: {{ $item['quantity'] }}</p>
                                </div>
                                <span class="shrink-0 text-sm font-bold text-qaid-navy">EGP {{ number_format((float) $item['subtotal'], 2) }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">{{ __('checkout.no_items') }}</p>
                        @endforelse
                    </div>
                    <div class="mt-4 space-y-2 border-t border-qaid-gold/20 pt-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-600">{{ __('checkout.subtotal') }}</span>
                            <span class="font-semibold text-qaid-navy">EGP {{ number_format((float) $total, 2) }}</span>
                        </div>
                        <div x-show="form.delivery_option !== 'discuss'" class="flex justify-between gap-4">
                            <span class="text-slate-600">{{ __('checkout.delivery_fee') }}</span>
                            <span class="font-semibold text-qaid-navy">
                                <template x-if="isFirstOrder && deliveryPrice > 0">
                                    <span><span class="me-1 line-through text-slate-400" x-text="'EGP ' + deliveryPrice.toFixed(2)"></span>{{ __('checkout.free') }}</span>
                                </template>
                                <template x-if="!isFirstOrder || deliveryPrice <= 0">
                                    <span x-text="'EGP ' + deliveryPrice.toFixed(2)"></span>
                                </template>
                            </span>
                        </div>
                        @if(config('checkout.coupons_enabled', false))
                            <div x-show="coupon.applied" class="flex justify-between gap-4 text-qaid-gold-dark">
                                <span>{{ __('checkout.discount') }}</span>
                                <span x-text="'- EGP ' + couponDiscount().toFixed(2)"></span>
                            </div>
                        @endif
                        <div class="flex justify-between gap-4 border-t border-qaid-gold/20 pt-3 font-bold">
                            <span>{{ __('checkout.order_total') }}</span>
                            <span class="font-sans text-lg" x-text="'EGP ' + calculateTotal().toFixed(2)"></span>
                        </div>
                    </div>
                </div>
            </details>
        </div>

        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3 lg:gap-8">
            <div class="min-w-0 space-y-6 lg:col-span-2">
                <form id="checkout-form" @submit.prevent="submitOrder" novalidate class="space-y-6">
                    @csrf

                    @if($vacationSettings->enabled)
                        <div class="rounded-2xl border border-qaid-gold/40 bg-white px-5 py-4 text-qaid-navy shadow-sm" role="alert">
                            {{ $vacationSettings->message }}
                        </div>
                    @endif

                    <section class="rounded-2xl border border-qaid-gold/20 bg-white p-6 shadow-lg shadow-qaid-navy/10">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-qaid-cream text-qaid-gold-dark">
                                <i class="fas fa-user" aria-hidden="true"></i>
                            </span>
                            <div>
                                <p class="font-sans text-xs font-bold uppercase tracking-[0.14em] text-qaid-gold-dark">{{ __('checkout.section_personal') }}</p>
                                <h2 class="font-sans text-xl font-bold text-qaid-navy">{{ __('checkout.personal_info') }}</h2>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div>
                                <label for="name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                                    {{ __('checkout.full_name') }} <span class="text-offer" aria-hidden="true">*</span>
                                </label>
                                <input id="name" name="name" type="text" x-model="form.name" @blur="validateField('name')" autocomplete="name" aria-required="true" aria-describedby="name-error"
                                    :aria-invalid="errors.name ? 'true' : 'false'"
                                    placeholder="{{ __('checkout.full_name_placeholder') }}"
                                    class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-base text-qaid-navy outline-none transition duration-200 placeholder:text-slate-400 focus-visible:border-qaid-gold focus-visible:ring-2 focus-visible:ring-qaid-gold/40">
                                <p id="name-error" x-show="errors.name" x-text="errors.name" class="mt-1.5 text-sm text-red-700" role="alert" aria-live="polite"></p>
                            </div>
                            <div>
                                <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                                    {{ __('checkout.email_address') }} <span class="text-offer" aria-hidden="true">*</span>
                                </label>
                                <input id="email" name="email" type="email" x-model="form.email" @blur="validateField('email')" autocomplete="email" aria-required="true" aria-describedby="email-error"
                                    :aria-invalid="errors.email ? 'true' : 'false'"
                                    placeholder="{{ __('checkout.email_placeholder') }}"
                                    class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-base text-qaid-navy outline-none transition duration-200 placeholder:text-slate-400 focus-visible:border-qaid-gold focus-visible:ring-2 focus-visible:ring-qaid-gold/40">
                                <p id="email-error" x-show="errors.email" x-text="errors.email" class="mt-1.5 text-sm text-red-700" role="alert" aria-live="polite"></p>
                            </div>
                            <div>
                                <label for="checkout_phone" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                                    {{ __('checkout.phone_number') }} <span class="text-offer" aria-hidden="true">*</span>
                                </label>
                                <input id="checkout_phone" name="phone" type="tel" inputmode="tel" x-model="form.phone" @blur="validateField('phone')" autocomplete="tel" aria-required="true" aria-describedby="phone-error"
                                    :aria-invalid="errors.phone ? 'true' : 'false'"
                                    placeholder="{{ __('checkout.phone_placeholder') }}"
                                    class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-base text-qaid-navy outline-none transition duration-200 placeholder:text-slate-400 focus-visible:border-qaid-gold focus-visible:ring-2 focus-visible:ring-qaid-gold/40">
                                <p id="phone-error" x-show="errors.phone" x-text="errors.phone" class="mt-1.5 text-sm text-red-700" role="alert" aria-live="polite"></p>
                            </div>
                            <div>
                                <label for="city_id" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                                    {{ __('checkout.city') }} <span class="text-offer" aria-hidden="true">*</span>
                                </label>
                                <select id="city_id" name="city_id" x-ref="citySelect" x-model="form.city_id" @change="onCityChange" aria-required="true" aria-describedby="city-error"
                                    :aria-invalid="errors.city_id ? 'true' : 'false'"
                                    class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-base text-qaid-navy outline-none transition duration-200 focus-visible:border-qaid-gold focus-visible:ring-2 focus-visible:ring-qaid-gold/40">
                                    <option value="">{{ __('checkout.city_placeholder') }}</option>
                                    @foreach(getCities() as $city)
                                        <option value="{{ $city['id'] }}"
                                            data-has-discussion="{{ $city['has_discussion_for_delivery'] ? 'true' : 'false' }}"
                                            data-delivery-price="{{ $city['price'] ?? 0 }}">
                                            {{ $city['value'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <p id="city-error" x-show="errors.city_id" x-text="errors.city_id" class="mt-1.5 text-sm text-red-700" role="alert" aria-live="polite"></p>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="address" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                                    {{ __('checkout.address') }} <span class="text-offer" aria-hidden="true">*</span>
                                </label>
                                <textarea id="address" name="address" x-model="form.address" @blur="validateField('address')" autocomplete="street-address" rows="2" aria-required="true" aria-describedby="address-error"
                                    :aria-invalid="errors.address ? 'true' : 'false'"
                                    placeholder="{{ __('checkout.address_placeholder') }}"
                                    class="min-h-12 w-full resize-y rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-base text-qaid-navy outline-none transition duration-200 placeholder:text-slate-400 focus-visible:border-qaid-gold focus-visible:ring-2 focus-visible:ring-qaid-gold/40"></textarea>
                                <p id="address-error" x-show="errors.address" x-text="errors.address" class="mt-1.5 text-sm text-red-700" role="alert" aria-live="polite"></p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-qaid-gold/20 bg-white p-6 shadow-lg shadow-qaid-navy/10">
                        <div class="mb-4 flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-qaid-cream text-qaid-gold-dark">
                                <i class="fas fa-pen" aria-hidden="true"></i>
                            </span>
                            <div>
                                <p class="font-sans text-xs font-bold uppercase tracking-[0.14em] text-qaid-gold-dark">{{ __('checkout.section_optional') }}</p>
                                <h2 class="font-sans text-xl font-bold text-qaid-navy">{{ __('checkout.delivery_notes') }}</h2>
                            </div>
                        </div>
                        <label for="notes" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">{{ __('checkout.customer_notes') }}</label>
                        <textarea id="notes" name="notes" x-model="form.notes" rows="3" placeholder="{{ __('checkout.customer_notes_placeholder') }}"
                            class="min-h-24 w-full resize-y rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-base text-qaid-navy outline-none transition duration-200 placeholder:text-slate-400 focus-visible:border-qaid-gold focus-visible:ring-2 focus-visible:ring-qaid-gold/40"></textarea>
                        <p class="mt-2 text-xs leading-5 text-slate-500">{{ __('checkout.customer_notes_hint') }}</p>
                    </section>

                    <section class="rounded-2xl border border-qaid-gold/20 bg-white p-6 shadow-lg shadow-qaid-navy/10">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-qaid-cream text-qaid-gold-dark">
                                <i class="fas fa-credit-card" aria-hidden="true"></i>
                            </span>
                            <div>
                                <p class="font-sans text-xs font-bold uppercase tracking-[0.14em] text-qaid-gold-dark">{{ __('checkout.section_payment') }}</p>
                                <h2 class="font-sans text-xl font-bold text-qaid-navy">{{ __('checkout.payment_method') }} <span class="text-offer" aria-hidden="true">*</span></h2>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <label class="flex min-h-24 cursor-pointer items-start gap-3 rounded-xl border p-4 transition duration-200"
                                :class="form.payment_method === 'cash_on_delivery' ? 'border-qaid-navy bg-qaid-cream/60 ring-1 ring-qaid-gold/50' : 'border-slate-200 bg-white hover:border-qaid-gold/70'">
                                <input type="radio" name="payment_method" value="cash_on_delivery" x-model="form.payment_method" @change="validateField('payment_method'); validateField('payment_proof')" class="mt-1 h-4 w-4 accent-qaid-navy">
                                <span class="min-w-0">
                                    <span class="block font-bold text-qaid-navy">{{ __('checkout.cash_on_delivery') }}</span>
                                    <span class="mt-1 block text-sm leading-5 text-slate-600">{{ __('checkout.pay_when_receive') }}</span>
                                </span>
                            </label>

                            <label class="relative flex min-h-24 items-start gap-3 rounded-xl border p-4 transition duration-200 {{ config('checkout.instapay_enabled', false) ? 'cursor-pointer border-slate-200 bg-white hover:border-qaid-gold/70' : 'cursor-not-allowed border-slate-200 bg-slate-50 opacity-75' }}"
                                :class="form.payment_method === 'instapay' ? 'border-qaid-navy bg-qaid-cream/60 ring-1 ring-qaid-gold/50' : ''"
                                aria-disabled="{{ config('checkout.instapay_enabled', false) ? 'false' : 'true' }}">
                                <input type="radio" name="payment_method" value="instapay" x-model="form.payment_method" @change="validateField('payment_method'); validateField('payment_proof')"
                                    @disabled(!config('checkout.instapay_enabled', false))
                                    class="mt-1 h-4 w-4 accent-qaid-navy">
                                <span class="min-w-0 flex-1">
                                    <span class="flex flex-wrap items-center gap-2">
                                        <span class="font-bold text-qaid-navy">{{ __('checkout.instapay') }}</span>
                                        @unless(config('checkout.instapay_enabled', false))
                                            <span class="rounded-full border border-qaid-gold/50 bg-qaid-cream px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-qaid-gold-dark">{{ __('checkout.coming_soon') }}</span>
                                        @endunless
                                    </span>
                                    <span class="mt-1 block text-sm leading-5 text-slate-600">{{ __('checkout.pay_instantly') }}</span>
                                </span>
                            </label>
                        </div>
                        <p id="payment-method-error" x-show="errors.payment_method" x-text="errors.payment_method" class="mt-2 text-sm text-red-700" role="alert" aria-live="polite"></p>

                        @if(config('checkout.instapay_enabled', false))
                            <div x-show="form.payment_method === 'instapay'" class="mt-4 space-y-4">
                                <div>
                                    <label for="insta_account" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                                        {{ __('checkout.instapay_account') }} <span class="text-offer" aria-hidden="true">*</span>
                                    </label>
                                    <input id="insta_account" type="text" x-model="form.insta_account" autocomplete="off" aria-describedby="insta-account-error"
                                        :aria-invalid="errors.insta_account ? 'true' : 'false'"
                                        placeholder="{{ __('checkout.instapay_account_placeholder') }}"
                                        class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-base text-qaid-navy outline-none transition duration-200 focus-visible:border-qaid-gold focus-visible:ring-2 focus-visible:ring-qaid-gold/40">
                                    <p id="insta-account-error" x-show="errors.insta_account" x-text="errors.insta_account" class="mt-1.5 text-sm text-red-700" role="alert" aria-live="polite"></p>
                                </div>
                                <div>
                                    <label for="payment_proof" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                                        {{ __('checkout.payment_proof_title') }} <span class="text-offer" aria-hidden="true">*</span>
                                    </label>
                                    <input id="payment_proof" name="payment_proof" type="file" accept="image/jpeg,image/png,image/webp" @change="setPaymentProof($event)"
                                        class="block min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-600 file:me-3 file:min-h-12 file:border-0 file:bg-qaid-cream file:px-4 file:font-semibold file:text-qaid-navy focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-qaid-gold/50">
                                    <p class="mt-1 text-xs text-slate-500">{{ __('checkout.payment_proof_hint') }}</p>
                                    <p x-show="errors.payment_proof" x-text="errors.payment_proof" class="mt-1.5 text-sm text-red-700" role="alert" aria-live="polite"></p>
                                </div>
                            </div>
                        @endif
                    </section>

                    <section class="rounded-2xl border border-qaid-gold/20 bg-white p-6 shadow-lg shadow-qaid-navy/10">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-qaid-cream text-qaid-gold-dark">
                                <i class="fas fa-tag" aria-hidden="true"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="font-sans text-xl font-bold text-qaid-navy">{{ __('checkout.coupon_code') }}</h2>
                                    @unless(config('checkout.coupons_enabled', false))
                                        <span class="rounded-full border border-qaid-gold/50 bg-qaid-cream px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-qaid-gold-dark">{{ __('checkout.coming_soon') }}</span>
                                    @endunless
                                </div>
                                <p class="mt-1 text-sm leading-5 text-slate-600">{{ __('checkout.coupons_disabled_description') }}</p>
                            </div>
                        </div>
                        @if(config('checkout.coupons_enabled', false))
                            <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                                <input type="text" x-model="coupon.code" autocomplete="off" placeholder="{{ __('checkout.coupon_placeholder') }}" class="h-12 min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3 uppercase text-qaid-navy outline-none focus-visible:border-qaid-gold focus-visible:ring-2 focus-visible:ring-qaid-gold/40">
                                <button type="button" @click="applyCoupon()" :disabled="coupon.loading" class="min-h-12 rounded-xl bg-qaid-navy px-5 font-bold text-qaid-gold-text transition hover:bg-qaid-navy-hover disabled:opacity-50">{{ __('checkout.apply_coupon') }}</button>
                            </div>
                            <p x-show="coupon.error" x-text="coupon.error" class="mt-2 text-sm text-red-700" role="alert" aria-live="polite"></p>
                        @endif
                    </section>

                    <section class="rounded-2xl border border-qaid-gold/20 bg-white p-6 shadow-lg shadow-qaid-navy/10">
                        <div class="mb-4 flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-qaid-cream text-qaid-gold-dark">
                                <i class="fas fa-truck" aria-hidden="true"></i>
                            </span>
                            <div>
                                <p class="font-sans text-xs font-bold uppercase tracking-[0.14em] text-qaid-gold-dark">{{ __('checkout.section_delivery') }}</p>
                                <h2 class="font-sans text-xl font-bold text-qaid-navy">{{ __('checkout.delivery_information') }}</h2>
                            </div>
                        </div>

                        @if($orderSettings?->has_delivery_option)
                            <div x-show="showDeliveryOptions && selectedCityHasDiscussion" class="mb-4 space-y-3">
                                <label class="flex min-h-16 cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-qaid-gold/70"
                                    :class="form.delivery_option === 'discuss' ? 'border-qaid-navy bg-qaid-cream/60 ring-1 ring-qaid-gold/50' : ''">
                                    <input type="radio" name="delivery_option" value="discuss" x-model="form.delivery_option" @change="validateField('delivery_option')" class="mt-1 h-4 w-4 accent-qaid-navy">
                                    <span>
                                        <span class="block font-bold text-qaid-navy">{{ __('checkout.discuss_delivery') }}</span>
                                        <span class="mt-1 block text-sm leading-5 text-slate-600">{{ __('checkout.discuss_delivery_desc') }}</span>
                                    </span>
                                </label>
                                <label x-show="form.delivery_option === 'discuss'" class="flex min-h-16 cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-qaid-gold/70"
                                    :class="form.delivery_option === 'proceed' ? 'border-qaid-navy bg-qaid-cream/60 ring-1 ring-qaid-gold/50' : ''">
                                    <input type="radio" name="delivery_option" value="proceed" x-model="form.delivery_option" @change="validateField('delivery_option')" class="mt-1 h-4 w-4 accent-qaid-navy">
                                    <span>
                                        <span class="block font-bold text-qaid-navy">{{ __('checkout.proceed_with_delivery') }}</span>
                                        <span class="mt-1 block text-sm leading-5 text-slate-600">{{ __('checkout.proceed_delivery_desc') }}</span>
                                    </span>
                                </label>
                                <p x-show="errors.delivery_option" x-text="errors.delivery_option" class="text-sm text-red-700" role="alert" aria-live="polite"></p>
                            </div>
                        @endif

                        <div class="rounded-xl border border-qaid-gold/25 bg-qaid-cream/70 p-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-circle-info mt-0.5 text-qaid-gold-dark" aria-hidden="true"></i>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-qaid-navy">{{ __('checkout.delivery_included') }}</p>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ __('checkout.delivery_fee_auto_added') }}:
                                        <template x-if="isFirstOrder && deliveryPrice > 0 && form.delivery_option !== 'discuss'">
                                            <span class="font-bold text-qaid-navy"><span class="me-1 line-through text-slate-400" x-text="'EGP ' + deliveryPrice.toFixed(2)"></span>{{ __('checkout.free') }}</span>
                                        </template>
                                        <template x-if="(!isFirstOrder || deliveryPrice <= 0) && form.delivery_option !== 'discuss'">
                                            <span class="font-bold text-qaid-navy" x-text="'EGP ' + deliveryPrice.toFixed(2)"></span>
                                        </template>
                                        <span x-show="form.delivery_option === 'discuss'">{{ __('checkout.delivery_cost_confirmed_later') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <p x-show="serverError" x-text="serverError" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert" aria-live="assertive"></p>
                    <p x-show="success" class="rounded-xl border border-qaid-gold/30 bg-white px-4 py-3 text-sm font-semibold text-qaid-navy" role="status" aria-live="polite">
                        {{ __('checkout.order_success') }}
                        <span class="ms-1">{{ __('checkout.order_id') }}:</span>
                        <span x-text="orderId"></span>
                    </p>
                </form>
            </div>

            <aside class="hidden min-w-0 lg:col-span-1 lg:block">
                <div class="checkout-summary-sticky rounded-2xl border border-qaid-gold/25 bg-white p-5 shadow-xl shadow-qaid-navy/10 xl:p-6">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-qaid-cream text-qaid-gold-dark">
                            <i class="fas fa-receipt" aria-hidden="true"></i>
                        </span>
                        <h2 class="font-sans text-xl font-bold text-qaid-navy">{{ __('checkout.order_summary') }}</h2>
                    </div>

                    <div class="max-h-72 space-y-4 overflow-y-auto pe-1">
                        @forelse($checkoutItems as $item)
                            @php($checkoutImage = filled($item['image'] ?? null) ? $item['image'] : data_get($item, 'images.0.image'))
                            <div class="flex items-center gap-3">
                                <div class="checkout-item-image flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-qaid-gold/20 bg-white text-slate-400">
                                    @if($imageUrl = storage_image_url($checkoutImage))
                                        <img src="{{ $imageUrl }}" alt="" class="h-full w-full object-contain p-1" onerror="this.hidden = true; this.nextElementSibling.hidden = false">
                                        <span hidden class="flex h-full w-full items-center justify-center bg-slate-50">
                                            <i class="fas fa-image" aria-hidden="true"></i>
                                        </span>
                                    @else
                                        <span class="flex h-full w-full items-center justify-center bg-slate-50">
                                            <i class="fas fa-image" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1 text-start">
                                    <p class="line-clamp-2 text-sm font-semibold leading-5 text-qaid-navy">{{ $item['name'] }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ __('checkout.quantity') }}: {{ $item['quantity'] }}</p>
                                </div>
                                <span class="shrink-0 text-sm font-bold text-qaid-navy">EGP {{ number_format((float) $item['subtotal'], 2) }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">{{ __('checkout.no_items') }}</p>
                        @endforelse
                    </div>

                    <div class="mt-5 space-y-3 border-t border-qaid-gold/20 pt-4">
                        <div class="flex justify-between gap-4 text-sm">
                            <span class="text-slate-600">{{ __('checkout.subtotal') }}</span>
                            <span class="font-semibold text-qaid-navy">EGP {{ number_format((float) $total, 2) }}</span>
                        </div>
                        <div x-show="form.delivery_option !== 'discuss'" class="flex justify-between gap-4 text-sm">
                            <span class="text-slate-600">{{ __('checkout.delivery_fee') }}</span>
                            <span class="font-semibold text-qaid-navy">
                                <template x-if="isFirstOrder && deliveryPrice > 0">
                                    <span><span class="me-1 line-through text-slate-400" x-text="'EGP ' + deliveryPrice.toFixed(2)"></span>{{ __('checkout.free') }}</span>
                                </template>
                                <template x-if="!isFirstOrder || deliveryPrice <= 0">
                                    <span x-text="'EGP ' + deliveryPrice.toFixed(2)"></span>
                                </template>
                            </span>
                        </div>
                        @if(config('checkout.coupons_enabled', false))
                            <div x-show="coupon.applied" class="flex justify-between gap-4 text-sm text-qaid-gold-dark">
                                <span>{{ __('checkout.discount') }}</span>
                                <span x-text="'- EGP ' + couponDiscount().toFixed(2)"></span>
                            </div>
                        @endif
                        <div class="flex items-baseline justify-between gap-3 border-t border-qaid-gold/20 pt-4">
                            <span class="text-sm font-semibold text-slate-700">{{ __('checkout.order_total') }}</span>
                            <span class="font-sans text-xl font-bold text-qaid-navy" x-text="'EGP ' + calculateTotal().toFixed(2)"></span>
                        </div>
                    </div>

                    @unless($vacationSettings->enabled)
                        <button type="submit" form="checkout-form" :disabled="loading || success"
                            class="mt-5 inline-flex h-[52px] w-full items-center justify-center gap-2 rounded-xl bg-qaid-navy px-4 font-bold text-qaid-gold-text shadow-md shadow-qaid-navy/15 transition duration-200 hover:bg-qaid-navy-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-qaid-gold focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                            <i x-show="!loading" class="fas fa-lock text-sm" aria-hidden="true"></i>
                            <span x-show="!loading">{{ __('checkout.place_order') }}</span>
                            <span x-show="loading" class="inline-flex items-center gap-2">
                                <i class="fas fa-spinner animate-spin" aria-hidden="true"></i>
                                {{ __('checkout.processing') }}
                            </span>
                        </button>
                        <p class="mt-3 text-center text-xs text-slate-500">
                            <i class="fas fa-shield-halved me-1 text-qaid-gold-dark" aria-hidden="true"></i>
                            {{ __('checkout.secure_information') }}
                        </p>
                    @endunless

                    <a href="{{ route('cart') }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 text-sm font-semibold text-slate-600 transition hover:text-qaid-navy">
                        <i class="fas fa-arrow-left rtl:rotate-180" aria-hidden="true"></i>
                        {{ __('checkout.back_to_cart') }}
                    </a>
                </div>
            </aside>
        </div>
    </div>

    @unless($vacationSettings->enabled)
        <div class="checkout-sticky-actions fixed inset-x-0 bottom-0 z-50 border-t border-qaid-gold/25 bg-white/95 px-4 pt-3 shadow-[0_-8px_24px_rgba(13,27,42,0.12)] backdrop-blur-md [padding-bottom:calc(0.75rem+env(safe-area-inset-bottom))] lg:hidden">
            <button type="submit" form="checkout-form" :disabled="loading || success"
                class="inline-flex h-[52px] w-full items-center justify-center gap-2 rounded-xl bg-qaid-navy px-4 font-bold text-qaid-gold-text transition duration-200 hover:bg-qaid-navy-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-qaid-gold focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                <i x-show="!loading" class="fas fa-lock text-sm" aria-hidden="true"></i>
                <span x-show="!loading">{{ __('checkout.place_order') }}</span>
                <span x-show="loading" class="inline-flex items-center gap-2">
                    <i class="fas fa-spinner animate-spin" aria-hidden="true"></i>
                    {{ __('checkout.processing') }}
                </span>
            </button>
            <p class="mt-2 text-center text-[11px] text-slate-500">
                <i class="fas fa-shield-halved me-1 text-qaid-gold-dark" aria-hidden="true"></i>
                {{ __('checkout.secure_information') }}
            </p>
        </div>
    @endunless
</section>
@endsection
