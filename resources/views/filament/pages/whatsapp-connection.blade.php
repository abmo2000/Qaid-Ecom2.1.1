<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->content }}

        <div wire:poll.25s="refreshConnection" class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Connection status</x-slot>

            <div class="flex items-center gap-3">
                <x-filament::badge :color="$state === 'open' ? 'success' : ($state === 'connecting' ? 'warning' : 'danger')">
                    {{ $state === 'open' ? 'Connected' : ($state === 'connecting' ? 'Connecting' : 'Disconnected') }}
                </x-filament::badge>
                @if ($phone)
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $phone }}</span>
                @endif
            </div>

            @if ($state !== 'open')
                <div class="mt-4 rounded-lg border border-warning-300 bg-warning-50 p-4 text-sm text-warning-800 dark:border-warning-700 dark:bg-warning-950 dark:text-warning-200">
                    WhatsApp confirmations are unavailable until the business number is connected.
                </div>
            @endif
        </x-filament::section>

        @if ($state !== 'open')
            <x-filament::section>
                <x-slot name="heading">Scan QR code</x-slot>
                <x-slot name="description">Open WhatsApp Business on the designated phone, then scan this code from Linked Devices.</x-slot>

                @if ($qrCode)
                    <div class="flex justify-center py-4">
                        <img src="{{ $qrCode }}" alt="WhatsApp connection QR code" class="h-72 w-72 rounded-lg border bg-white p-3" />
                    </div>
                @else
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $error ?: 'Waiting for a QR code...' }}</p>
                @endif

                <x-filament::button wire:click="reconnect" icon="heroicon-o-arrow-path" class="mt-4">
                    Reconnect
                </x-filament::button>
            </x-filament::section>
        @endif
        </div>
    </div>
</x-filament-panels::page>