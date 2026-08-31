<div id="signinModal" x-data="signinForm()" class="hidden fixed inset-0 backdrop-blur-lg bg-[#0d1b2a]/70 z-50 flex items-center justify-center p-4">
    <div class="bg-[#0d1b2a] border border-[#d8b35a]/35 rounded-2xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-black uppercase tracking-wide text-[#f7d879]">{{ trans('auth.sign_in') }}</h2>
                <button @click="closeModal()" class="text-white/40 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Success Message -->
            <div x-show="success" x-transition class="mb-4 bg-[#d8b35a]/10 border border-[#d8b35a]/40 text-[#f7d879] px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 me-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ trans('login_success') }}</span>
                </div>
            </div>

            <form @submit.prevent="submitSignin">

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-white/80 font-semibold mb-2 text-sm uppercase tracking-wide">{{ trans('auth.email') }} <span class="text-[#d8b35a]">*</span></label>
                    <input
                        type="email"
                        id="email"
                        x-model="form.email"
                        :class="{ 'border-red-500': errors.email }"
                        @blur="validateField('email')"
                        class="w-full px-4 py-3 border border-[#d8b35a]/30 bg-white/5 text-white rounded-xl focus:outline-none focus:border-[#d8b35a] focus:ring-2 focus:ring-[#d8b35a]/40 transition-all duration-300 placeholder-white/30"
                        placeholder="{{ trans('auth.email-placeholder') }}"
                    >
                    <p x-show="errors.email" x-text="errors.email" class="text-red-400 text-sm mt-1"></p>
                </div>

                <!-- Password -->
                <div x-data="{ show: false }" class="mb-6 relative">
                    <label for="password" class="block text-white/80 font-semibold mb-2 text-sm uppercase tracking-wide">{{ trans('auth.pass')}} <span class="text-[#d8b35a]">*</span></label>
                    <input
                         :type="show ? 'text' : 'password'"
                        id="password"
                        x-model="form.password"
                        :class="{ 'border-red-500': errors.password }"
                        @blur="validateField('password')"
                        class="w-full px-4 py-3 border border-[#d8b35a]/30 bg-white/5 text-white rounded-xl focus:outline-none focus:border-[#d8b35a] focus:ring-2 focus:ring-[#d8b35a]/40 transition-all duration-300 placeholder-white/30"
                        placeholder="{{ trans('auth.password-placeholder') }}"
                    >

                    <button
                        type="button"
                        @click="show = !show"
                        class="absolute inset-y-0 top-7 ltr:right-0 rtl:left-0 ltr:pr-3 rtl:pl-3 flex items-center text-white/40 hover:text-[#d8b35a] transition"
                    >
                        <!-- Eye Icon (show password) -->
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye Slash Icon (hide password) -->
                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                    <p x-show="errors.password" x-text="errors.password" class="text-red-400 text-sm mt-1"></p>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="loading"
                    :class="loading ? 'bg-white/10 text-white/40 cursor-not-allowed' : 'bg-[#d8b35a] hover:bg-[#f7d879] text-[#0d1b2a]'"
                    class="w-full cursor-pointer font-black uppercase tracking-wider py-3.5 px-10 rounded-xl shadow-lg hover:shadow-[#d8b35a]/40 transition-all duration-300 transform hover:scale-[1.02]"
                >
                    <span x-show="!loading">Sign In</span>
                    <span x-show="loading" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 me-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ trans('auth.signing_in') }}
                    </span>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-[#d8b35a]/25"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-[#0d1b2a] text-white/40">{{ trans('auth.continue-with') }}</span>
                </div>
            </div>

            <!-- Google Sign In -->
            <a href="{{  route('google.login')  }}" class="flex items-center justify-center px-4 py-3 bg-white/5 border border-[#d8b35a]/30 rounded-xl text-white hover:border-[#d8b35a] hover:bg-white/10 transition duration-200 cursor-pointer">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                {{ trans('auth.google') }}
            </a>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-white/60">
                    {{ trans('auth.have-account') }}
                    <a href="{{ route('register') }}" class="text-[#d8b35a] hover:text-[#f7d879] font-semibold transition">{{ trans('auth.sign_up') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function signinForm() {
    return {
        form: {
            email: '',
            password: ''
        },
        errors: {},
        loading: false,
        success: false,

        validateField(field) {
            this.errors[field] = '';

            if (field === 'email') {
                if (!this.form.email) {
                    this.errors.email = 'Email is required';
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) {
                    this.errors.email = 'Please enter a valid email';
                }
            }

            if (field === 'password') {
                if (!this.form.password) {
                    this.errors.password = 'Password is required';
                } else if (this.form.password.length < 6) {
                    this.errors.password = 'Password must be at least 6 characters';
                }
            }
        },

        validateForm() {
            this.validateField('email');
            this.validateField('password');

            return !this.errors.email && !this.errors.password;
        },

        async submitSignin() {
            if (!this.validateForm()) {
                return;
            }

            this.loading = true;

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (response.ok) {
                    this.success = true;
                    setTimeout(() => {
                        window.location.href = data.redirect || '/cart';
                    }, 1500);
                } else {
                    if (data.errors) {
                        this.errors.email = data.errors.email?.[0] || data.message || 'Invalid credentials';
                    } else {
                        this.errors.email = data.message || 'Invalid credentials';
                    }
                }
            } catch (error) {
                console.error('Signin error:', error);
                this.errors.email = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        },

        closeModal() {
            document.getElementById('signinModal').classList.add('hidden');
            this.form = { email: '', password: '' };
            this.errors = {};
            this.success = false;
        }
    }
}
</script>
