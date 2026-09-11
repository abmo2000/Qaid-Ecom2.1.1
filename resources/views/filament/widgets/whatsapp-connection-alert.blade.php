@if ($state !== 'open')
    <div wire:poll.30s="refreshConnection" class="fi-wi-widget">
        <x-filament::section>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-danger-700 dark:text-danger-300">WhatsApp is disconnected</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Order confirmations will not be sent until the business number is reconnected.</p>
                </div>
                @if (auth()->user()?->isSuperAdmin())
                    <x-filament::button tag="a" href="{{ $this->connectionUrl() }}" color="danger" icon="heroicon-o-arrow-right">
                        Connect WhatsApp
                    </x-filament::button>
                @endif
            </div>
        </x-filament::section>
    </div>
@endif