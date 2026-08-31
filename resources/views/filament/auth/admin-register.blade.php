<div class="fi-simple-page">
    <section class="grid auto-cols-fr gap-y-6">
        <header class="fi-simple-header flex flex-col items-center">
            <h1 class="fi-simple-header-heading text-center text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                {{ $this->getHeading() }}
            </h1>

            @if ($this->getSubheading())
                <p class="fi-simple-header-subheading mt-2 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ $this->getSubheading() }}
                </p>
            @endif
        </header>

        @if ($this->registrationSuccessful)
            <div class="rounded-xl bg-success-50 p-4 ring-1 ring-success-200 dark:bg-success-400/10 dark:ring-success-400/20">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-heroicon-s-check-circle class="h-5 w-5 text-success-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-success-800 dark:text-success-200">
                            Registration Submitted!
                        </h3>
                        <div class="mt-2 text-sm text-success-700 dark:text-success-300">
                            <p>Your admin account has been created and is pending approval by the Super Admin. You will be able to log in once your account is approved.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ filament()->getLoginUrl() }}" class="fi-link fi-link-size-sm inline-flex items-center justify-center gap-x-1 text-sm font-semibold text-primary-600 outline-none hover:underline focus-visible:underline dark:text-primary-400">
                    &larr; Back to Login
                </a>
            </div>
        @else
            <x-filament-panels::form id="form" wire:submit="register">
                {{ $this->form }}

                <x-filament::button type="submit" class="w-full">
                    Register
                </x-filament::button>
            </x-filament-panels::form>

            <div class="text-center">
                <a href="{{ filament()->getLoginUrl() }}" class="fi-link fi-link-size-sm inline-flex items-center justify-center gap-x-1 text-sm font-semibold text-primary-600 outline-none hover:underline focus-visible:underline dark:text-primary-400">
                    Already have an account? Sign in
                </a>
            </div>
        @endif
    </section>
</div>
