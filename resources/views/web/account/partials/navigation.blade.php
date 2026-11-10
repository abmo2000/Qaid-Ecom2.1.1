@php
    $initial = mb_strtoupper(mb_substr(trim($user->name), 0, 1));
    $navLinkClass = 'flex min-h-11 items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition duration-200';
@endphp

<nav aria-label="{{ __('profile.account_navigation') }}" class="min-w-0">
    <div class="mb-4 overflow-x-auto rounded-2xl border border-[#d8b35a]/30 bg-white/90 p-2 shadow-sm lg:hidden">
        <div class="flex min-w-max items-center gap-2">
            <a href="{{ route('account.profile.edit') }}"
                @class([$navLinkClass, 'bg-[#0d1b2a] text-[#f7d879]' => $active === 'profile', 'text-slate-600 hover:bg-[#f7f1e3] hover:text-[#0d1b2a]' => $active !== 'profile'])>
                <i class="fas fa-user-circle w-4 shrink-0" aria-hidden="true"></i>
                {{ __('profile.profile') }}
            </a>
            <a href="{{ route('account.orders.index') }}"
                @class([$navLinkClass, 'bg-[#0d1b2a] text-[#f7d879]' => $active === 'orders', 'text-slate-600 hover:bg-[#f7f1e3] hover:text-[#0d1b2a]' => $active !== 'orders'])>
                <i class="fas fa-box w-4 shrink-0" aria-hidden="true"></i>
                {{ __('profile.orders') }}
            </a>
            <a href="{{ route('account.profile.edit') }}#security"
                class="{{ $navLinkClass }} text-slate-600 hover:bg-[#f7f1e3] hover:text-[#0d1b2a]">
                <i class="fas fa-shield-alt w-4 shrink-0" aria-hidden="true"></i>
                {{ __('profile.security') }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="{{ $navLinkClass }} text-red-700 hover:bg-red-50">
                    <i class="fas fa-sign-out-alt w-4 shrink-0" aria-hidden="true"></i>
                    {{ __('profile.logout') }}
                </button>
            </form>
        </div>
    </div>

    <div class="sticky top-6 hidden rounded-[2rem] border border-[#d8b35a]/30 bg-white p-6 text-center shadow-[0_22px_55px_rgba(13,27,42,.10)] lg:block">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border border-[#d8b35a]/50 bg-[#0d1b2a] text-3xl font-bold text-[#f7d879] shadow-lg" aria-hidden="true">
            {{ $initial }}
        </div>
        <h2 class="mt-4 break-words text-lg font-bold text-[#0d1b2a]">{{ $user->name }}</h2>
        <p class="mt-1 break-all text-sm text-slate-500">{{ $user->email }}</p>

        <div class="my-6 h-px bg-gradient-to-r from-transparent via-[#d8b35a]/60 to-transparent"></div>

        <div class="space-y-1.5 text-start">
            <a href="{{ route('account.profile.edit') }}"
                @class([$navLinkClass, 'border-s-2 border-[#b8913f] bg-[#f7f1e3] text-[#0d1b2a]' => $active === 'profile', 'text-slate-600 hover:bg-[#f7f1e3] hover:text-[#0d1b2a]' => $active !== 'profile'])>
                <i class="fas fa-user-circle w-4 text-[#9b762f]" aria-hidden="true"></i>
                {{ __('profile.profile') }}
            </a>
            <a href="{{ route('account.orders.index') }}"
                @class([$navLinkClass, 'border-s-2 border-[#b8913f] bg-[#f7f1e3] text-[#0d1b2a]' => $active === 'orders', 'text-slate-600 hover:bg-[#f7f1e3] hover:text-[#0d1b2a]' => $active !== 'orders'])>
                <i class="fas fa-box w-4 text-[#9b762f]" aria-hidden="true"></i>
                {{ __('profile.orders') }}
            </a>
            <a href="{{ route('account.profile.edit') }}#security"
                class="{{ $navLinkClass }} text-slate-600 hover:bg-[#f7f1e3] hover:text-[#0d1b2a]">
                <i class="fas fa-shield-alt w-4 text-[#9b762f]" aria-hidden="true"></i>
                {{ __('profile.security') }}
            </a>
            <div class="my-2 border-t border-slate-100"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="{{ $navLinkClass }} w-full text-red-700 hover:bg-red-50">
                    <i class="fas fa-sign-out-alt w-4" aria-hidden="true"></i>
                    {{ __('profile.logout') }}
                </button>
            </form>
        </div>
    </div>
</nav>
