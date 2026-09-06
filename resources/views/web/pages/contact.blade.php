@extends('web.layouts.main')

@section('title')
Contact
@endsection

@section('content')

<x-navbar></x-navbar>

<!-- Background Image Section -->
<section class="relative min-h-screen overflow-hidden bg-[#0d1b2a] pb-16 pt-32 sm:pt-36 md:pb-24 md:pt-40 lg:pt-44">
    <!-- Background Video with Overlay -->
    <div class="absolute right-0 left-0 top-0 h-full w-full">
        <video
            autoplay
            muted
            loop
            playsinline
            preload="auto"
            poster="{{ asset('assets/images/headers/contactBackground.jfif') }}"
            class="w-full h-full object-cover brightness-75 contrast-110"
        >
            <source src="{{ asset('assets/images/headers/Hero.mp4') }}" type="video/mp4">
        </video>
        <div class="absolute top-0 left-0 w-full h-full bg-[#0d1b2a]/60 bg-gradient-to-b from-[#0d1b2a]/50 via-[#0d1b2a]/50 to-[#0d1b2a]/80"></div>
    </div>

    <!-- Content -->
    <div class="container mx-auto px-4 relative z-10">
        <h2 class="heading text-[#f7d879] text-center text-4xl font-black uppercase tracking-wide mb-8 py-8">{{ trans('contact.contact') }}</h2>

        <!-- Contact Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto mb-10">

            <!-- Phone Card -->
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-2xl hover:shadow-[#d8b35a]/20 border border-[#d8b35a]/30 hover:border-[#d8b35a] transition-all duration-300 transform hover:-translate-y-1 p-6">
                <div class="flex items-start gap-4">
                    <div class="bg-[#d8b35a] text-[#0d1b2a] p-3.5 rounded-xl flex-shrink-0">
                        <i class="fas fa-phone text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-black uppercase tracking-wide text-white mb-1.5">{{ trans('auth.phone') }}</h3>
                        <p class="text-white/60">{{"+20". ' ' .getBuisnessSettings('buisness-info')?->mobile_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Email Card -->
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-2xl hover:shadow-[#d8b35a]/20 border border-[#d8b35a]/30 hover:border-[#d8b35a] transition-all duration-300 transform hover:-translate-y-1 p-6">
                <div class="flex items-start gap-4">
                    <div class="bg-[#d8b35a] text-[#0d1b2a] p-3.5 rounded-xl flex-shrink-0">
                        <i class="fas fa-envelope text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-black uppercase tracking-wide text-white mb-1.5">{{ trans('auth.email') }}</h3>
                        <p class="text-white/60">{{ getBuisnessSettings('buisness-info')?->email }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Contact Form -->
        <div class="max-w-3xl mx-auto">
            <div class="bg-white/5 backdrop-blur-md rounded-3xl shadow-2xl border border-[#d8b35a]/35 p-8 md:p-10">
                <h2 class="text-2xl md:text-3xl font-black uppercase tracking-wide text-[#f7d879] text-center mb-6">{{ trans('contact.send-message-header') }}</h2>

                @if(session('success'))
                    <div class="mb-6 rounded-xl bg-emerald-500/15 border border-emerald-400/50 text-emerald-300 px-5 py-3 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <x-errors></x-errors>

                <form action="{{ route('contact-message') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Name + Email side by side -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-white/80 font-semibold mb-2 text-sm uppercase tracking-wide">{{ trans('auth.name') }}</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="w-full px-4 py-3 border border-[#d8b35a]/30 bg-[#0d1b2a] text-white rounded-xl focus:outline-none focus:border-[#d8b35a] focus:ring-2 focus:ring-[#d8b35a]/40 transition-all duration-300 placeholder-white/30"
                                placeholder="{{ trans('auth.name-placeholder') }}">
                        </div>

                        <div>
                            <label for="email" class="block text-white/80 font-semibold mb-2 text-sm uppercase tracking-wide">{{ trans('auth.email') }}</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="w-full px-4 py-3 border border-[#d8b35a]/30 bg-[#0d1b2a] text-white rounded-xl focus:outline-none focus:border-[#d8b35a] focus:ring-2 focus:ring-[#d8b35a]/40 transition-all duration-300 placeholder-white/30"
                                placeholder="{{ trans('auth.email-placeholder') }}">
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-white/80 font-semibold mb-2 text-sm uppercase tracking-wide">{{ trans('auth.phone') }}</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-3 border border-[#d8b35a]/30 bg-[#0d1b2a] text-white rounded-xl focus:outline-none focus:border-[#d8b35a] focus:ring-2 focus:ring-[#d8b35a]/40 transition-all duration-300 placeholder-white/30"
                            placeholder="{{ trans('auth.phone-placeholder') }}">
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-white/80 font-semibold mb-2 text-sm uppercase tracking-wide">{{ trans('auth.message') }}</label>
                        <textarea id="message" name="message" rows="3"
                            class="w-full px-4 py-3 border border-[#d8b35a]/30 bg-[#0d1b2a] text-white rounded-xl focus:outline-none focus:border-[#d8b35a] focus:ring-2 focus:ring-[#d8b35a]/40 transition-all duration-300 resize-none placeholder-white/30"
                            placeholder="{{ trans('auth.message-placeholder') }}">{{ old('message') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center pt-1">
                        <button type="submit"
                            class="bg-[#d8b35a] hover:bg-[#f7d879] text-[#0d1b2a] font-black uppercase tracking-wider py-3.5 px-10 rounded-xl shadow-lg hover:shadow-[#d8b35a]/40 transition-all duration-300 transform hover:scale-[1.02]">
                            <i class="fas fa-paper-plane me-2"></i>
                            {{ trans('contact.send-message') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
