@extends('web.layouts.account')

@section('account-title', __('profile.title'))
@section('account-description', __('profile.intro'))
@section('account-active', 'profile')

@section('account-content')
    @if(session('status'))
        <div class="account-fade-in flex items-center gap-3 rounded-2xl border border-[#d8b35a]/40 bg-white px-5 py-4 text-sm font-semibold text-[#0d1b2a] shadow-sm" role="status">
            <i class="fas fa-check-circle text-[#9b762f]" aria-hidden="true"></i>
            {{ session('status') === 'password-updated' ? __('profile.password_updated') : __('profile.updated') }}
        </div>
    @endif

    <form action="{{ route('user-profile-information.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-[2rem] border border-[#d8b35a]/25 bg-white p-5 shadow-[0_22px_55px_rgba(13,27,42,.10)] sm:p-8">
            <div class="mb-6 flex items-start gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#f7f1e3] text-[#9b762f]">
                    <i class="fas fa-user" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="text-xl font-bold text-[#0d1b2a] sm:text-2xl">{{ __('profile.personal_information') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ __('profile.personal_information_help') }}</p>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-2 block text-xs font-black uppercase tracking-[0.16em] text-[#0d1b2a]">{{ __('profile.name') }}</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" required
                        aria-describedby="@error('name', 'updateProfileInformation') name-error @enderror"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-start text-[#0d1b2a] outline-none transition duration-200 placeholder:text-slate-400 focus:border-[#d8b35a] focus:ring-4 focus:ring-[#d8b35a]/20">
                    @error('name', 'updateProfileInformation')
                        <p id="name-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block text-xs font-black uppercase tracking-[0.16em] text-[#0d1b2a]">{{ __('profile.email') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" required
                        aria-describedby="@error('email', 'updateProfileInformation') email-error @enderror"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-start text-[#0d1b2a] outline-none transition duration-200 placeholder:text-slate-400 focus:border-[#d8b35a] focus:ring-4 focus:ring-[#d8b35a]/20">
                    @error('email', 'updateProfileInformation')
                        <p id="email-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="rounded-[2rem] border border-[#d8b35a]/25 bg-white p-5 shadow-[0_22px_55px_rgba(13,27,42,.10)] sm:p-8">
            <div class="mb-6 flex items-start gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#f7f1e3] text-[#9b762f]">
                    <i class="fas fa-address-card" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="text-xl font-bold text-[#0d1b2a] sm:text-2xl">{{ __('profile.contact_address') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ __('profile.contact_address_help') }}</p>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="phone" class="mb-2 block text-xs font-black uppercase tracking-[0.16em] text-[#0d1b2a]">{{ __('profile.phone') }}</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                        aria-describedby="@error('phone', 'updateProfileInformation') phone-error @enderror"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-start text-[#0d1b2a] outline-none transition duration-200 placeholder:text-slate-400 focus:border-[#d8b35a] focus:ring-4 focus:ring-[#d8b35a]/20">
                    @error('phone', 'updateProfileInformation')
                        <p id="phone-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="city_id" class="mb-2 block text-xs font-black uppercase tracking-[0.16em] text-[#0d1b2a]">{{ __('profile.city') }}</label>
                    <select id="city_id" name="city_id"
                        aria-describedby="@error('city_id', 'updateProfileInformation') city-error @enderror"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-start text-[#0d1b2a] outline-none transition duration-200 focus:border-[#d8b35a] focus:ring-4 focus:ring-[#d8b35a]/20">
                        <option value="">{{ __('profile.choose_city') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city['id'] }}" @selected((string) old('city_id', $user->city_id) === (string) $city['id'])>{{ $city['value'] }}</option>
                        @endforeach
                    </select>
                    @error('city_id', 'updateProfileInformation')
                        <p id="city-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="address" class="mb-2 block text-xs font-black uppercase tracking-[0.16em] text-[#0d1b2a]">{{ __('profile.address') }}</label>
                    <textarea id="address" name="address" rows="3" autocomplete="street-address"
                        aria-describedby="@error('address', 'updateProfileInformation') address-error @enderror"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-start text-[#0d1b2a] outline-none transition duration-200 placeholder:text-slate-400 focus:border-[#d8b35a] focus:ring-4 focus:ring-[#d8b35a]/20">{{ old('address', $user->address) }}</textarea>
                    @error('address', 'updateProfileInformation')
                        <p id="address-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="insta_account" class="mb-2 block text-xs font-black uppercase tracking-[0.16em] text-[#0d1b2a]">{{ __('profile.instapay_account') }}</label>
                    <input id="insta_account" name="insta_account" type="text" value="{{ old('insta_account', $user->insta_account) }}" autocomplete="off"
                        aria-describedby="@error('insta_account', 'updateProfileInformation') instapay-error @enderror"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-start text-[#0d1b2a] outline-none transition duration-200 placeholder:text-slate-400 focus:border-[#d8b35a] focus:ring-4 focus:ring-[#d8b35a]/20">
                    @error('insta_account', 'updateProfileInformation')
                        <p id="instapay-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-7 flex flex-col gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:items-center">
                <button type="submit" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-full bg-[#0d1b2a] px-7 py-3 text-sm font-black uppercase tracking-wider text-[#f7d879] shadow-lg shadow-[#0d1b2a]/15 transition duration-200 hover:-translate-y-0.5 hover:bg-[#13283d] focus:outline-none focus:ring-4 focus:ring-[#d8b35a]/30">
                    <i class="fas fa-check" aria-hidden="true"></i>
                    {{ __('profile.save_changes') }}
                </button>
                <a href="{{ route('account.orders.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-full border border-[#b8913f]/50 bg-white px-7 py-3 text-sm font-bold text-[#0d1b2a] transition duration-200 hover:border-[#b8913f] hover:bg-[#f7f1e3] focus:outline-none focus:ring-4 focus:ring-[#d8b35a]/20">
                    {{ __('profile.view_orders') }}
                </a>
            </div>
        </section>
    </form>

    <section id="security" class="scroll-mt-28 rounded-[2rem] border border-[#d8b35a]/25 bg-white p-5 shadow-[0_22px_55px_rgba(13,27,42,.10)] sm:p-8">
        <div class="mb-6 flex items-start gap-4">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#f7f1e3] text-[#9b762f]">
                <i class="fas fa-shield-alt" aria-hidden="true"></i>
            </span>
            <div>
                <h2 class="text-xl font-bold text-[#0d1b2a] sm:text-2xl">{{ __('profile.security') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('profile.security_help') }}</p>
            </div>
        </div>

        @if($user->google_id)
            <div class="mb-6 flex items-center gap-3 rounded-xl border border-[#d8b35a]/40 bg-[#f7f1e3] px-4 py-3 text-sm font-semibold text-[#0d1b2a]" role="status">
                <i class="fab fa-google text-[#9b762f]" aria-hidden="true"></i>
                {{ __('profile.google_connected') }}
            </div>
        @endif

        <form action="{{ route('user-password.update') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="mb-2 block text-xs font-black uppercase tracking-[0.16em] text-[#0d1b2a]">{{ __('profile.current_password') }}</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" required
                    aria-describedby="@error('current_password', 'updatePassword') current-password-error @enderror"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-start text-[#0d1b2a] outline-none transition duration-200 focus:border-[#d8b35a] focus:ring-4 focus:ring-[#d8b35a]/20 sm:max-w-xl">
                @error('current_password', 'updatePassword')
                    <p id="current-password-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-2 block text-xs font-black uppercase tracking-[0.16em] text-[#0d1b2a]">{{ __('profile.new_password') }}</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                        aria-describedby="@error('password', 'updatePassword') password-error @enderror"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-start text-[#0d1b2a] outline-none transition duration-200 focus:border-[#d8b35a] focus:ring-4 focus:ring-[#d8b35a]/20">
                    @error('password', 'updatePassword')
                        <p id="password-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-xs font-black uppercase tracking-[0.16em] text-[#0d1b2a]">{{ __('profile.confirm_password') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-start text-[#0d1b2a] outline-none transition duration-200 focus:border-[#d8b35a] focus:ring-4 focus:ring-[#d8b35a]/20">
                </div>
            </div>

            <button type="submit" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-full bg-[#0d1b2a] px-7 py-3 text-sm font-black uppercase tracking-wider text-[#f7d879] shadow-lg shadow-[#0d1b2a]/15 transition duration-200 hover:-translate-y-0.5 hover:bg-[#13283d] focus:outline-none focus:ring-4 focus:ring-[#d8b35a]/30">
                <i class="fas fa-lock" aria-hidden="true"></i>
                {{ __('profile.update_password') }}
            </button>
        </form>
    </section>
@endsection
