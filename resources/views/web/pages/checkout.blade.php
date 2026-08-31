@extends('web.layouts.main')

@section('title')
Checkout
@endsection

@section('content')

<x-navbar></x-navbar>
@php
    $defaultCityId = auth()->check() ? auth()->user()->city_id ?? '' : '';
    if ($defaultCityId) {
        $savedCity = \App\Models\City::find($defaultCityId);
        if ($savedCity) {
            // Query cities using the translation relationship
            $latestCity = \App\Models\City::query()
                ->whereHas('translations', function($query) use ($savedCity) {
                    $query->where('name', $savedCity->translations->first()?->name ?? '');
                })
                ->orderByDesc('id')
                ->first();
            if ($latestCity) {
                $defaultCityId = $latestCity->id;
            }
        }
    }
@endphp
<section class="checkout-page py-10 sm:py-14 md:py-20 bg-black min-h-screen" x-data="checkoutForm({{ $total }}, {
    name: '{{ auth()->check() ? addslashes(auth()->user()->name ?? '') : '' }}',
    email: '{{ auth()->check() ? auth()->user()->email ?? '' : '' }}',
    phone: '{{ auth()->check() ? auth()->user()->phone ?? '' : '' }}',
    address: '{{ auth()->check() ? addslashes(auth()->user()->address ?? '') : '' }}',
    'insta_account': '{{ auth()->check() ? addslashes(auth()->user()->insta_account ?? '') : '' }}',
    'city_id': '{{ $defaultCityId }}',
    'instapay': '{{ $buisnessSettings->instapay_account ?? '' }}',
    'isFirstOrder': '{{ $isFirstOrder }}',

})">
  <div class="container mx-auto px-3 sm:px-4">
    <h2 class="text-white text-center text-2xl sm:text-3xl md:text-4xl font-bold mb-8 sm:mb-12">{{ trans('checkout.checkout') }}</h2>

    <div class="max-w-5xl mx-auto">
      @guest
      <div class="mb-6 rounded-3xl border border-gray-700 bg-gray-900/80 p-6 text-white shadow-xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-2xl font-semibold">Checkout options</h3>
                <p class="mt-2 text-gray-400">You can continue as a guest, login to your account, or register a new buyer account before placing your order.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="#checkout-form" class="rounded-full bg-white/10 border border-white/20 px-5 py-3 text-sm font-semibold text-white hover:bg-orange-500 hover:border-orange-500 transition">Continue as guest</a>
                <a href="{{ route('login') }}" class="rounded-full bg-white/10 border border-white/20 px-5 py-3 text-sm font-semibold text-white hover:bg-orange-500 hover:border-orange-500 transition">Login</a>
                <a href="{{ route('register') }}" class="rounded-full bg-orange-500 px-5 py-3 text-sm font-semibold text-white hover:bg-orange-600 transition">Register</a>
            </div>
        </div>
      </div>
      @endguest

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

        {{-- LEFT COLUMN: Customer Information --}}
        <div class="lg:col-span-2">
          <div class="bg-gray-800/95 backdrop-blur-md rounded-2xl sm:rounded-3xl shadow-xl border border-gray-700 p-4 sm:p-6 md:p-8">

            <div x-show="success" x-transition class="mb-4 bg-green-900 border border-green-700 text-green-200 px-4 py-3 rounded-lg">
              <div class="flex items-center">
                <svg class="w-5 h-5 me-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ trans('checkout.order_success') }}</span>
              </div>
            </div>

            <form id="checkout-form" @submit.prevent="submitOrder" class="space-y-5 sm:space-y-6">
              @csrf

              {{-- Section: Personal Info --}}
              <div>
                <h3 class="text-white text-lg font-semibold mb-4 flex items-center gap-2">
                  <i class="fas fa-user text-orange-500"></i>
                  {{ trans('checkout.personal_info') }}
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {{-- Full Name --}}
                  <div>
                    <label for="name" class="block text-gray-300 text-sm font-medium mb-2">
                      {{ trans('checkout.full_name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" x-model="form.name" @blur="validateField('name')"
                      :placeholder="'{{ trans('checkout.full_name_placeholder') ?? 'John Doe' }}'" required
                      class="w-full px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                      :class="{ 'border-red-500': errors.name }">
                    <p x-show="errors.name" x-text="errors.name" class="text-red-500 text-sm mt-1"></p>
                  </div>

                  {{-- Email --}}
                  <div>
                    <label for="email" class="block text-gray-300 text-sm font-medium mb-2">
                      {{ trans('checkout.email_address') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" x-model="form.email" @blur="validateField('email')"
                      :placeholder="'{{ trans('checkout.email_placeholder') ?? 'john@example.com' }}'" required
                      class="w-full px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                      :class="{ 'border-red-500': errors.email }">
                    <p x-show="errors.email" x-text="errors.email" class="text-red-500 text-sm mt-1"></p>
                  </div>

                  {{-- Phone --}}
                  <div>
                    <label for="checkout_phone" class="block text-gray-300 text-sm font-medium mb-2">
                      {{ trans('checkout.phone_number') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" id="checkout_phone" name="phone" x-model="form.phone"
                      :placeholder="'{{ trans('checkout.phone_placeholder') ?? '+201148992811' }}'" required
                      class="w-full px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                      :class="{ 'border-red-500': errors.phone }">
                    <p x-show="errors.phone" x-text="errors.phone" class="text-red-500 text-sm mt-1"></p>
                  </div>

                  {{-- City --}}
                  <div>
                    <label for="city_id" class="block text-gray-300 text-sm font-medium mb-2">
                      {{ trans('checkout.city') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="city_id" name="city_id" x-ref="citySelect" x-model="form.city_id" @change="onCityChange" required
                      class="w-full px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                      :class="{ 'border-red-500': errors.city_id }">
                      <option value="">{{ trans('checkout.city_placeholder') ?? 'Select your city' }}</option>
                      @foreach(getCities() as $city)
                        <option value="{{ $city['id'] }}"
                          data-has-discussion="{{ $city['has_discussion_for_delivery'] ? 'true' : 'false' }}"
                          data-delivery-price="{{ $city['price'] ?? 0 }}">
                          {{ $city['value'] }}
                        </option>
                      @endforeach
                    </select>
                    <p x-show="errors.city_id" x-text="errors.city_id" class="text-red-500 text-sm mt-1"></p>
                  </div>
                </div>

                {{-- Address (full width) --}}
                <div class="mt-4">
                  <label for="address" class="block text-gray-300 text-sm font-medium mb-2">
                    {{ trans('checkout.address') }} <span class="text-red-500">*</span>
                  </label>
                  <textarea id="address" name="address" x-model="form.address" @blur="validateField('address')"
                    :placeholder="'{{ trans('checkout.address_placeholder') ?? 'مثال : شارع النصر ، مدينة نصر ، القاهرة' }}'" rows="2"
                    class="w-full px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition resize-none"
                    :class="{ 'border-red-500': errors.address }"></textarea>
                  <p x-show="errors.address" x-text="errors.address" class="text-red-500 text-sm mt-1"></p>
                </div>
              </div>

              {{-- Divider --}}
              <div class="border-t border-gray-700"></div>

              {{-- Section: Customer Notes --}}
              <div>
                <h3 class="text-white text-lg font-semibold mb-4 flex items-center gap-2">
                  <i class="fas fa-sticky-note text-orange-500"></i>
                  {{ trans('checkout.customer_notes') }}
                </h3>
                <textarea id="notes" name="notes" x-model="form.notes"
                  :placeholder="'{{ trans('checkout.customer_notes_placeholder') }}'" rows="3"
                  class="w-full px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition resize-none"></textarea>
                <p class="text-gray-500 text-xs mt-1">{{ trans('checkout.customer_notes_hint') }}</p>
              </div>

              {{-- Divider --}}
              <div class="border-t border-gray-700"></div>

              {{-- Section: Coupon Code --}}
              <div>
                <h3 class="text-white text-lg font-semibold mb-4 flex items-center gap-2">
                  <i class="fas fa-tag text-orange-500"></i>
                  {{ trans('checkout.coupon_code') }}
                </h3>

                {{-- Applied coupon success state --}}
                <div x-show="coupon.applied" x-transition class="flex items-center justify-between p-3 bg-green-900/40 border border-green-600 rounded-lg mb-3">
                  <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-green-300 text-sm font-medium" x-text="coupon.message"></span>
                  </div>
                  <button type="button" @click="removeCoupon()"
                    class="text-gray-400 hover:text-red-400 text-xs font-medium transition ml-3 underline">
                    {{ trans('checkout.remove_coupon') }}
                  </button>
                </div>

                {{-- Coupon input row --}}
                <div x-show="!coupon.applied" class="flex gap-2">
                  <input type="text" x-model="coupon.code"
                    @keydown.enter.prevent="applyCoupon()"
                    :placeholder="'{{ trans('checkout.coupon_placeholder') }}'"
                    class="flex-1 px-4 py-2.5 text-sm bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 uppercase focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': coupon.error }">
                  <button type="button" @click="applyCoupon()" :disabled="coupon.loading"
                    class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0">
                    <span x-show="!coupon.loading">{{ trans('checkout.apply_coupon') }}</span>
                    <span x-show="coupon.loading">
                      <svg class="animate-spin h-4 w-4 inline" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                      </svg>
                    </span>
                  </button>
                </div>
                <p x-show="coupon.error" x-text="coupon.error" class="text-red-400 text-sm mt-1.5"></p>
              </div>

              {{-- Divider --}}
              <div class="border-t border-gray-700"></div>

              @if($orderSettings?->has_delivery_option)
              {{-- Section: Delivery Options --}}
              <div x-show="showDeliveryOptions && selectedCityHasDiscussion" x-transition class="space-y-3">
                <h3 class="text-white text-lg font-semibold mb-3 flex items-center gap-2">
                  <i class="fas fa-truck text-orange-500"></i>
                  {{ trans('checkout.delivery_options') }}
                </h3>

                <div>
                  <label class="flex items-start p-3 sm:p-4 bg-gray-900 border rounded-lg cursor-pointer hover:border-orange-500 transition"
                    :class="form.delivery_option === 'discuss' ? 'border-orange-500' : 'border-gray-600'">
                    <input type="radio" x-model="form.delivery_option" @change="validateField('delivery_option')" value="discuss"
                      class="mt-1 text-orange-500 focus:ring-orange-500 focus:ring-2">
                    <div class="ms-3">
                      <p class="text-white font-medium">{{ trans('checkout.discuss_delivery') }}</p>
                      <p class="text-gray-400 text-sm mt-1">{{ trans('checkout.discuss_delivery_desc') }}</p>
                    </div>
                  </label>
                </div>

                <div x-show="form.delivery_option == 'discuss'">
                  <label class="flex items-start p-3 sm:p-4 bg-gray-900 border rounded-lg cursor-pointer hover:border-orange-500 transition"
                    :class="form.delivery_option === 'proceed' ? 'border-orange-500' : 'border-gray-600'">
                    <input type="radio" x-model="form.delivery_option" @change="validateField('delivery_option')" value="proceed"
                      class="mt-1 text-orange-500 focus:ring-orange-500 focus:ring-2">
                    <div class="ms-3 flex-1">
                      <p class="text-white font-medium">{{ trans('checkout.proceed_with_delivery') }}</p>
                      <p class="text-gray-400 text-sm mt-1">
                        {{ trans('checkout.proceed_delivery_desc') }}
                        <span x-show="deliveryPrice > 0" class="text-orange-400 font-semibold">
                          <template x-if="isFirstOrder">
                            <span>
                              <span class="text-gray-500 line-through mr-2">(+ EGP <span x-text="deliveryPrice.toFixed(2)"></span>)</span>
                              <span class="text-green-400 font-semibold">(FREE Delivery - First Order!)</span>
                            </span>
                          </template>
                          <template x-if="! isFirstOrder">
                            <span class="text-orange-400 font-semibold">
                              (+ EGP <span x-text="deliveryPrice.toFixed(2)"></span>)
                            </span>
                          </template>
                        </span>
                      </p>
                    </div>
                  </label>
                </div>

                <p x-show="errors.delivery_option" x-text="errors.delivery_option" class="text-red-500 text-sm mt-1"></p>
              </div>

              <div x-show="showDeliveryOptions && !selectedCityHasDiscussion && deliveryPrice > 0" x-transition class="p-4 bg-blue-900 bg-opacity-30 border border-blue-600 rounded-lg">
                <div class="flex items-start">
                  <svg class="w-5 h-5 text-blue-400 mt-0.5 me-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                  </svg>
                  <div class="flex-1">
                    <span x-show="! isFirstOrder">{{ trans('checkout.delivery_included') }}</span>
                    <span x-show="isFirstOrder">First Order  - Free Delivery!</span>
                    <p class="text-xs mt-1" :class="isFirstOrder ? 'text-green-300' : 'text-blue-300'">
                      <template x-if="isFirstOrder">
                        <span>
                          {{ trans('checkout.delivery_fee_auto_added') }}: 
                          <span class="line-through text-gray-400 mr-2">EGP <span x-text="deliveryPrice.toFixed(2)"></span></span>
                          <span class="font-semibold text-green-400">FREE</span>
                        </span>
                      </template>
                      <template x-if="! isFirstOrder">
                        <span>
                          {{ trans('checkout.delivery_fee_auto_added') }}: 
                          <span class="font-semibold">EGP <span x-text="deliveryPrice.toFixed(2)"></span></span>
                        </span>
                      </template>
                    </p>
                  </div>
                </div>
              </div>
              @else
              <div x-show="deliveryPrice > 0" x-transition class="p-4 bg-blue-900 bg-opacity-30 border border-blue-600 rounded-lg">
                <div class="flex items-start">
                  <svg class="w-5 h-5 text-blue-400 mt-0.5 me-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                  </svg>
                  <div class="flex-1">
                    <span x-show="! isFirstOrder">{{ trans('checkout.delivery_included') }}</span>
                    <span x-show="isFirstOrder">First Order  - Free Delivery!</span>
                    <p class="text-xs mt-1" :class="isFirstOrder ? 'text-green-300' : 'text-blue-300'">
                      <template x-if="isFirstOrder">
                        <span>
                          {{ trans('checkout.delivery_fee_auto_added') }}: 
                          <span class="line-through text-gray-400 mr-2">EGP <span x-text="deliveryPrice.toFixed(2)"></span></span>
                          <span class="font-semibold text-green-400">FREE</span>
                        </span>
                      </template>
                      <template x-if="! isFirstOrder">
                        <span>
                          {{ trans('checkout.delivery_fee_auto_added') }}: 
                          <span class="font-semibold">EGP <span x-text="deliveryPrice.toFixed(2)"></span></span>
                        </span>
                      </template>
                    </p>
                  </div>
                </div>
              </div>
              @endif

              {{-- Divider --}}
              <div class="border-t border-gray-700"></div>

              {{-- Section: Payment Method --}}
              <div>
                <h3 class="text-white text-lg font-semibold mb-4 flex items-center gap-2">
                  <i class="fas fa-credit-card text-orange-500"></i>
                  {{ trans('checkout.payment_method') }} <span class="text-red-500">*</span>
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <label class="flex items-start p-3 sm:p-4 bg-gray-900 border rounded-lg cursor-pointer hover:border-orange-500 transition"
                    :class="form.payment_method === 'cash_on_delivery' ? 'border-orange-500' : 'border-gray-600'">
                    <input type="radio" x-model="form.payment_method" @change="validateField('payment_method')" value="cash_on_delivery"
                      class="mt-1 text-orange-500 focus:ring-orange-500 focus:ring-2">
                    <div class="ms-3">
                      <p class="text-white font-medium">{{ trans('checkout.cash_on_delivery') }}</p>
                      <p class="text-gray-400 text-sm mt-1">{{ trans('checkout.pay_when_receive') }}</p>
                    </div>
                  </label>

                  <label class="flex items-start p-3 sm:p-4 bg-gray-900 border rounded-lg cursor-pointer hover:border-orange-500 transition"
                    :class="form.payment_method === 'instapay' ? 'border-orange-500' : 'border-gray-600'">
                    <input type="radio" x-model="form.payment_method" @change="validateField('payment_method')" value="instapay"
                      class="mt-1 text-orange-500 focus:ring-orange-500 focus:ring-2">
                    <div class="ms-3">
                      <p class="text-white font-medium">{{ trans('checkout.instapay') }}</p>
                      <p class="text-gray-400 text-sm mt-1">{{ trans('checkout.pay_instantly') }}</p>
                    </div>
                  </label>
                </div>
                
                <p x-show="errors.payment_method" x-text="errors.payment_method" class="text-red-500 text-sm mt-2"></p>
              </div>

              <div x-show="form.payment_method === 'instapay'" x-transition>
                <label for="insta_account" class="block text-gray-300 text-sm font-medium mb-2">
                  {{ trans('checkout.instapay_account') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="insta_account" x-model="form.insta_account" @blur="validateField('insta_account')"
                  :placeholder="'{{ trans('checkout.instapay_account_placeholder') ?? 'example@instapay.com' }}'"
                  class="w-full px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                  :class="{ 'border-red-500': errors.insta_account }">
                <p x-show="errors.insta_account" x-text="errors.insta_account" class="text-red-500 text-sm mt-1"></p>
              </div>

              <div class="rounded-xl border border-orange-500/30 bg-orange-500/10 p-4">
                <label for="payment_proof" class="block text-gray-200 text-sm font-medium mb-2">
                  {{ trans('checkout.payment_proof_title') }} <span class="text-red-500">*</span>
                </label>
                <div class="relative rounded-xl border-2 border-dashed border-orange-400/70 bg-gray-950/60 p-4 text-center transition hover:border-orange-400" 
                     @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false" @drop.prevent="handleDrop($event)"
                     :class="dragOver ? 'border-orange-300 bg-orange-500/20' : ''">
                  <input id="payment_proof" name="payment_proof" type="file" accept="image/*" class="absolute inset-0 h-full w-full cursor-pointer opacity-0" @change="setPaymentProof($event); validateField('payment_proof')">
                  <div class="flex flex-col items-center justify-center gap-2">
                    <i class="fas fa-cloud-arrow-up text-2xl text-orange-400"></i>
                    <p class="text-sm font-semibold text-white">{{ trans('checkout.payment_proof_dropzone') }}</p>
                    <p class="text-xs text-gray-400">{{ trans('checkout.payment_proof_hint') }}</p>
                    <div class="mt-2 w-full" x-show="form.payment_proof">
                      <div class="rounded-lg border border-orange-500/30 bg-black/30 px-3 py-2 text-sm text-orange-200">
                        <p class="font-medium">{{ __('Selected file') }}: <span x-text="form.payment_proof?.name || ''"></span></p>
                      </div>
                      <img x-show="proofPreviewUrl" :src="proofPreviewUrl" alt="Payment proof preview"
                        class="mt-3 mx-auto h-32 w-full max-w-xs rounded-lg border border-orange-500/40 object-cover">
                    </div>
                  </div>
                </div>
                <p x-show="errors.payment_proof" x-text="errors.payment_proof" class="text-red-500 text-sm mt-2"></p>
              </div>

              {{-- Place Order Button (mobile only - below form) --}}
              <div class="lg:hidden">
                <div class="pt-4 border-t border-gray-700 space-y-3">
                  <div x-show="form.delivery_option !== 'discuss'" class="flex justify-between items-center">
                    <span class="text-gray-400">{{ trans('checkout.delivery_fee') }}:</span>
                    <span>
                      <template x-if="isFirstOrder">
                        <span>
                          <span class="line-through text-gray-500 mr-2">EGP <span x-text="deliveryPrice.toFixed(2)"></span></span>
                          <span class="text-green-400 font-semibold">FREE</span>
                        </span>
                      </template>
                      <template x-if="! isFirstOrder">
                        <span class="text-gray-400">EGP <span x-text="deliveryPrice.toFixed(2)"></span></span>
                      </template>
                    </span>
                  </div>
                  <div x-show="coupon.applied" class="flex justify-between items-center text-green-400">
                    <span class="text-sm">{{ trans('checkout.discount') }} (<span x-text="coupon.discountPercentage"></span>%):</span>
                    <span class="font-semibold text-sm">- EGP <span x-text="couponDiscount().toFixed(2)"></span></span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-300 text-base sm:text-lg font-medium">{{ trans('checkout.order_total') }}:</span>
                    <span class="text-green-500 text-xl sm:text-2xl font-bold" x-text="'EGP ' + calculateTotal().toFixed(2)"></span>
                  </div>
                  <button type="submit" :disabled="loading"
                    class="w-full py-3.5 sm:py-4 bg-orange-600 hover:bg-orange-700 text-white text-base sm:text-lg font-semibold rounded-lg transition duration-300 shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-orange-500 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading">{{ trans('checkout.place_order') }}</span>
                    <span x-show="loading" class="flex items-center justify-center">
                      <svg class="animate-spin h-5 w-5 me-3" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ trans('checkout.processing') }}...
                    </span>
                  </button>
                </div>
              </div>

              {{-- Success Message --}}
              <div x-show="success" x-transition class="mt-4 p-4 bg-green-900 border border-green-600 rounded-lg text-green-200">
                <p class="font-semibold">{{ trans('checkout.order_success') }}</p>
                <p class="text-sm mt-1">{{ trans('checkout.order_id') }}: <span x-text="orderId"></span></p>
              </div>

            </form>
          </div>
        </div>

        {{-- RIGHT COLUMN: Order Summary (sticky sidebar on desktop) --}}
        <div class="hidden lg:block">
          <div class="bg-gray-800/95 backdrop-blur-md rounded-2xl sm:rounded-3xl shadow-xl border border-gray-700 p-6 sticky top-20">
            <h3 class="text-white text-lg font-semibold mb-5 flex items-center gap-2">
              <i class="fas fa-receipt text-orange-500"></i>
              {{ trans('checkout.order_summary') }}
            </h3>

            <div class="space-y-3">
              <div x-show="form.delivery_option !== 'discuss'" class="flex justify-between items-center">
                <span class="text-gray-400 text-sm">{{ trans('checkout.delivery_fee') }}:</span>
                <span>
                  <template x-if="isFirstOrder">
                    <span>
                      <span class="line-through text-gray-500 mr-1 text-sm">EGP <span x-text="deliveryPrice.toFixed(2)"></span></span>
                      <span class="text-green-400 font-semibold text-sm">FREE</span>
                    </span>
                  </template>
                  <template x-if="! isFirstOrder">
                    <span class="text-gray-400 text-sm">EGP <span x-text="deliveryPrice.toFixed(2)"></span></span>
                  </template>
                </span>
              </div>

              <div x-show="coupon.applied" class="flex justify-between items-center text-green-400">
                <span class="text-sm">{{ trans('checkout.discount') }} (<span x-text="coupon.discountPercentage"></span>%):</span>
                <span class="font-semibold text-sm">- EGP <span x-text="couponDiscount().toFixed(2)"></span></span>
              </div>

              <div class="border-t border-gray-700 pt-3 flex justify-between items-center">
                <span class="text-white font-semibold">{{ trans('checkout.order_total') }}:</span>
                <span class="text-green-500 text-2xl font-bold" x-text="'EGP ' + calculateTotal().toFixed(2)"></span>
              </div>
            </div>

            <button type="submit" form="checkout-form" :disabled="loading" @click="$root.querySelector('form').requestSubmit()"
              class="w-full mt-6 py-3.5 bg-orange-600 hover:bg-orange-700 text-white text-lg font-semibold rounded-lg transition duration-300 shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-orange-500 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed">
              <span x-show="!loading">{{ trans('checkout.place_order') }}</span>
              <span x-show="loading" class="flex items-center justify-center">
                <svg class="animate-spin h-5 w-5 me-3" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ trans('checkout.processing') }}...
              </span>
            </button>

            <div x-show="success" x-transition class="mt-4 p-3 bg-green-900 border border-green-600 rounded-lg text-green-200 text-sm">
              <p class="font-semibold">{{ trans('checkout.order_success') }}</p>
              <p class="mt-1">{{ trans('checkout.order_id') }}: <span x-text="orderId"></span></p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection