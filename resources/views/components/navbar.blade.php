<nav
    x-data="{
        open: false,
        userDropdown: false,
        scrolled: false,
        cartCount: {{ $cartCount }},
        init() {
            this.scrolled = window.pageYOffset > 50;

            window.addEventListener('scroll', () => {
                this.scrolled = window.pageYOffset > 50;
            });

            window.addEventListener('cart-updated', (e) => {
                this.cartCount = e.detail.count;
            });

        },
        scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);

            if (section) {
                const navHeight = this.scrolled ? 76 : 110;
                const elementPosition = section.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - navHeight;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                this.open = false;
            }
        }
    }"
   :class="scrolled ? 'top-2 px-4 sm:top-3 sm:px-5' : 'top-3 px-4 sm:top-4 sm:px-5'"
    class="fixed start-0 end-0 z-[9990] transition-all duration-300"
>
    <!-- Pill Bar -->
    <div
 :class="scrolled
        ? 'max-w-[92%] border-[#d8b35a]/25 bg-white/95 shadow-xl shadow-[#0d1b2a]/10 sm:max-w-[calc(100vw-1.5rem)] md:max-w-[50rem] xl:max-w-[58rem]'
        : 'max-w-[92%] border-white/70 bg-white/80 shadow-lg shadow-[#0d1b2a]/5 sm:max-w-[calc(100vw-1.5rem)] md:max-w-[55rem] xl:max-w-[64rem]'"
        class="relative mx-auto overflow-visible rounded-full border backdrop-blur-xl transition-all duration-300"
    >
        <div
            :class="scrolled ? 'h-14 px-3 sm:h-[3.75rem] sm:px-5 lg:h-16 lg:px-6' : 'h-16 px-3 sm:h-18 sm:px-5 lg:h-20 lg:px-7'"
            class="flex items-center justify-between gap-3 transition-all duration-300"
        >
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3">
                <img
                    :class="scrolled ? 'h-9 w-9 sm:h-10 sm:w-10' : 'h-11 w-11 sm:h-12 sm:w-12'"
                    class="rounded-full object-cover ring-2 ring-white shadow-md transition-all duration-300"
                    src="{{ asset('assets/images/logos/logo.jpg') }}"
                    alt="QAID logo"
                >
            </a>

            <!-- Desktop Menu -->
            <div
                :class="scrolled ? 'gap-4' : 'gap-5'"
                class="hidden min-w-0 items-center justify-center transition-all duration-300 xl:flex"
            >
                <a href="{{ route('home') }}" class="nav-link">{{ trans('navbar.home') }}</a>
                <a href="{{ route('shop') }}" class="nav-link">{{ trans('navbar.shop') }}</a>
                <a href="{{ route('contact') }}" class="nav-link">{{ trans('navbar.contact') }}</a>
                <a href="{{ route('wholesale-sales') }}" class="nav-link">{{ trans('wholesale.title') }}</a>

                @if(Request::is('/'))
                    <a href="#fearured-products" @click.prevent="scrollToSection('fearured-products')" class="nav-link cursor-pointer">
                        {{ trans('navbar.featured-products') }}
                    </a>
                @endif
            </div>

            <!-- Actions -->
            <div
                :class="scrolled ? 'gap-1.5 sm:gap-2' : 'gap-2 sm:gap-3'"
                class="flex shrink-0 items-center transition-all duration-300"
            >
                @unless(auth()->check() && auth()->user()->isAdmin())
                    <!-- Cart -->
                    <a
                        href="{{ route('cart') }}"
                        :class="scrolled ? 'h-9 w-9 sm:h-10 sm:w-10' : 'h-10 w-10 sm:h-11 sm:w-11'"
                        class="relative inline-flex items-center justify-center rounded-full bg-slate-100 text-[#0d1b2a] transition hover:bg-[#0d1b2a] hover:text-[#f7d879]"
                    >
                        <i class="fas fa-shopping-bag text-lg sm:text-xl"></i>

                        <span
                            x-show="cartCount > 0"
                            x-text="cartCount"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-50"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="absolute -end-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#0d1b2a] px-1 text-[11px] font-bold leading-none text-[#f7d879] shadow-lg"
                        ></span>
                    </a>
                @endunless

                @guest
                    <div class="relative z-[9999]" @click.away="userDropdown = false">
                        <button
                            @click="userDropdown = !userDropdown"
                            :class="scrolled ? 'h-9 w-9 sm:h-10 sm:w-10' : 'h-10 w-10 sm:h-11 sm:w-11'"
                            class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-[#0d1b2a] transition hover:bg-[#0d1b2a] hover:text-[#f7d879] focus:outline-none focus:ring-2 focus:ring-[#d8b35a]/30"
                            aria-label="Account"
                        >
                            <i class="fas fa-user text-base sm:text-lg"></i>
                        </button>

                        <div
                            x-cloak
                            x-show="userDropdown"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                            class="absolute end-0 top-full z-[10000] mt-3 w-52 overflow-hidden rounded-2xl border border-[#d8b35a]/25 bg-white py-2 shadow-[0_28px_80px_rgba(13,27,42,.22)]"
                        >
                            <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-[#f7f1e3] hover:text-[#0d1b2a]">
                                <i class="fas fa-right-to-bracket w-4 text-[#9b762f]"></i>
                                <span>Login</span>
                            </a>

                            <a href="{{ route('register') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-[#f7f1e3] hover:text-[#0d1b2a]">
                                <i class="fas fa-user-plus w-4 text-[#9b762f]"></i>
                                <span>Register</span>
                            </a>
                        </div>
                    </div>
                @endguest

                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="relative z-[9999]" @click.away="userDropdown = false">
                            <button
                                @click="userDropdown = !userDropdown"
                                :class="scrolled ? 'h-9 w-9 sm:h-10 sm:w-10' : 'h-10 w-10 sm:h-11 sm:w-11'"
                                class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-[#0d1b2a] transition hover:bg-[#0d1b2a] hover:text-[#f7d879] focus:outline-none focus:ring-2 focus:ring-[#d8b35a]/30"
                            >
                                <i class="fas fa-user-shield text-base sm:text-lg"></i>
                            </button>

                            <div
                                x-cloak
                                x-show="userDropdown"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                class="absolute end-0 top-full z-[10000] mt-3 w-60 overflow-hidden rounded-2xl border border-[#d8b35a]/25 bg-white py-2 shadow-[0_28px_80px_rgba(13,27,42,.22)]"
                            >
                                <a href="/admin" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-[#f7f1e3] hover:text-[#0d1b2a]">
                                    <i class="fas fa-tachometer-alt w-4 text-[#9b762f]"></i>
                                    <span>Admin Dashboard</span>
                                </a>

                                <a href="/admin/orders" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-[#f7f1e3] hover:text-[#0d1b2a]">
                                    <i class="fas fa-clipboard-list w-4 text-[#9b762f]"></i>
                                    <span>Orders</span>
                                </a>

                                <a href="/admin/customers" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-[#f7f1e3] hover:text-[#0d1b2a]">
                                    <i class="fas fa-users w-4 text-[#9b762f]"></i>
                                    <span>Customers</span>
                                </a>

                                <a href="/admin/cities" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-[#f7f1e3] hover:text-[#0d1b2a]">
                                    <i class="fas fa-truck w-4 text-[#9b762f]"></i>
                                    <span>Delivery Pricing</span>
                                </a>

                                <a href="/admin/products" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-[#f7f1e3] hover:text-[#0d1b2a]">
                                    <i class="fas fa-box-open w-4 text-[#9b762f]"></i>
                                    <span>Products</span>
                                </a>

                                <div class="my-1 border-t border-slate-200"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt w-4"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="relative z-[9999]" @click.away="userDropdown = false">
                            <button
                                @click="userDropdown = !userDropdown"
                                :class="scrolled ? 'h-9 w-9 sm:h-10 sm:w-10' : 'h-10 w-10 sm:h-11 sm:w-11'"
                                class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-[#0d1b2a] transition hover:bg-[#0d1b2a] hover:text-[#f7d879] focus:outline-none focus:ring-2 focus:ring-[#d8b35a]/30"
                            >
                                <i class="fas fa-user text-base sm:text-lg"></i>
                            </button>

                            <div
                                x-cloak
                                x-show="userDropdown"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                class="absolute end-0 top-full z-[10000] mt-3 w-52 overflow-hidden rounded-2xl border border-[#d8b35a]/25 bg-white py-2 shadow-[0_28px_80px_rgba(13,27,42,.22)]"
                            >
                                <a href="{{ route('account.profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-[#f7f1e3] hover:text-[#0d1b2a]">
                                    <i class="fas fa-user-circle w-4 text-[#9b762f]"></i>
                                    <span>Profile</span>
                                </a>

                                <a href="{{ route('account.orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-[#f7f1e3] hover:text-[#0d1b2a]">
                                    <i class="fas fa-box w-4 text-[#9b762f]"></i>
                                    <span>My Orders</span>
                                </a>

                                <div class="my-1 border-t border-slate-200"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt w-4"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth

                <!-- Language Desktop / Tablet -->
                <div class="hidden sm:block">
                    <select
                        x-on:change="window.location.href = $event.target.value"
                        :class="scrolled ? 'h-9 px-3 text-xs sm:h-10' : 'h-10 px-3 text-sm sm:h-11 lg:px-4'"
                        class="cursor-pointer appearance-none rounded-full border border-slate-200 bg-slate-100 font-bold text-[#0d1b2a] transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#d8b35a]/30"
                    >
                        <option value="{{ route('lang-switch', 'en') }}" {{ app()->getLocale() === 'en' ? 'selected' : '' }} class="bg-white text-slate-900">
                            EN
                        </option>

                        <option value="{{ route('lang-switch', 'ar') }}" {{ app()->getLocale() === 'ar' ? 'selected' : '' }} class="bg-white text-slate-900">
                            AR
                        </option>
                    </select>
                </div>

                <!-- Hamburger -->
                <button
                    x-on:click="open = !open"
                    :class="scrolled ? 'h-9 w-9 sm:h-10 sm:w-10' : 'h-10 w-10 sm:h-11 sm:w-11'"
                    class="inline-flex items-center justify-center rounded-full bg-[#0d1b2a] text-[#f7d879] transition hover:bg-[#13283d] focus:outline-none focus:ring-2 focus:ring-[#d8b35a]/30 xl:hidden"
                    aria-label="Toggle menu"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Menu Panel (independent of the pill's rounded-full shape) -->
    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
        @click.away="open = false"
        class="relative mx-auto mt-2 max-w-[92%] overflow-hidden rounded-3xl border border-slate-200/70 bg-white/95 shadow-xl shadow-[#0d1b2a]/10 backdrop-blur-xl sm:max-w-[calc(100vw-1.5rem)] md:max-w-[50rem] xl:hidden"
    >
        <div class="grid gap-1 px-3 py-3 sm:grid-cols-2 sm:px-5 lg:grid-cols-3">
            <a href="{{ route('home') }}" class="mobile-nav-link">{{ trans('navbar.home') }}</a>
            <a href="{{ route('shop') }}" class="mobile-nav-link">{{ trans('navbar.shop') }}</a>
            <a href="{{ route('contact') }}" class="mobile-nav-link">{{ trans('navbar.contact') }}</a>

            @if(Request::is('/'))
                <a href="#fearured-products" @click.prevent="scrollToSection('fearured-products')" class="mobile-nav-link">
                    {{ trans('navbar.featured-products') }}
                </a>
            @endif

            @auth
                @if(auth()->user()->isAdmin())
                    <a href="/admin" class="mobile-nav-link">Admin Dashboard</a>
                    <a href="/admin/orders" class="mobile-nav-link">Orders</a>
                    <a href="/admin/customers" class="mobile-nav-link">Customers</a>
                    <a href="/admin/cities" class="mobile-nav-link">Delivery Pricing</a>
                    <a href="/admin/products" class="mobile-nav-link">Products</a>
                @endif
            @endauth

            @guest
                <a href="{{ route('login') }}" class="mobile-nav-link">Login</a>
                <a href="{{ route('register') }}" class="mobile-nav-link">Register</a>
            @endguest

            <div class="pt-2 sm:hidden">
                <select
                    x-on:change="window.location.href = $event.target.value"
                    class="h-12 w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-100 px-4 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#d8b35a]/30"
                >
                    <option value="{{ route('lang-switch', 'en') }}" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>
                        English
                    </option>

                    <option value="{{ route('lang-switch', 'ar') }}" {{ app()->getLocale() === 'ar' ? 'selected' : '' }}>
                        العربية
                    </option>
                </select>
            </div>
        </div>
    </div>
</nav>
