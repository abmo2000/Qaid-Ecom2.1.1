<?php

namespace App\Filament\Pages;

use App\Jobs\LogSecurityEventJob;
use App\Models\BuisnessSetting;
use App\Services\EvolutionWhatsAppService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class WhatsAppConnection extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;
    protected static ?string $navigationLabel = 'WhatsApp Connection';
    protected static ?string $title = 'WhatsApp Connection';
    protected static string|UnitEnum|null $navigationGroup = 'Business Settings';
    protected static ?int $navigationSort = 6;
    protected string $view = 'filament.pages.whatsapp-connection';

    public string $state = 'close';
    public ?string $phone = null;
    public ?string $qrCode = null;
    public ?string $error = null;
    public ?string $lastAuditedState = null;
    public array $templateData = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() === true;
    }

    public function mount(EvolutionWhatsAppService $whatsApp): void
    {
        $this->loadTemplates();
        $this->refreshConnection($whatsApp);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Message Template')
                    ->description('Customize the confirmation sent after a successful order. Available tags: {customer_name}, {order_number}, {order_total}.')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        Tabs::make('Languages')
                            ->tabs([
                                $this->templateTab('English', 'en', false),
                                $this->templateTab('Arabic', 'ar', true),
                            ])
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('templateData');
    }

    public function saveTemplate(): void
    {
        $data = $this->form->getState();
        $setting = BuisnessSetting::firstOrCreate(['key' => 'whatsapp-message']);

        foreach (['en', 'ar'] as $locale) {
            $setting->translateOrNew($locale)
                ->fill(['value' => ['message' => $data[$locale]['message'] ?? '']])
                ->save();
        }

        $this->templateData = $data;

        $notification = Notification::make()
            ->title('WhatsApp message template saved')
            ->success();

        if (! $this->containsPlaceholder($data['en']['message'] ?? '') || ! $this->containsPlaceholder($data['ar']['message'] ?? '')) {
            $notification
                ->title('Template saved without placeholders')
                ->body('Consider adding at least one available tag so messages remain personalized.')
                ->warning();
        }

        $notification->send();
    }

    public function refreshConnection(?EvolutionWhatsAppService $whatsApp = null): void
    {
        $whatsApp ??= app(EvolutionWhatsAppService::class);
        $connection = $whatsApp->connectionState();

        $newState = $connection['state'];

        if ($this->lastAuditedState !== null && $this->lastAuditedState !== $newState) {
            $this->audit('state_changed', $newState);
        }

        $this->state = $newState;
        $this->lastAuditedState = $newState;
        $this->phone = $connection['phone'];
        $this->error = null;

        if ($this->state !== 'open') {
            $this->qrCode = $whatsApp->connect();
            $this->error = $this->qrCode === null ? 'Evolution API is unavailable or not configured.' : null;
        } else {
            $this->qrCode = null;
        }
    }

    public function reconnect(): void
    {
        $this->qrCode = app(EvolutionWhatsAppService::class)->connect();
        $this->state = 'connecting';
        $this->audit('reconnect_requested', $this->state);

        $notification = Notification::make()
            ->title($this->qrCode ? 'Fresh QR code generated' : 'Could not generate a QR code');

        ($this->qrCode ? $notification->success() : $notification->danger())->send();
    }

    public function disconnect(): void
    {
        $loggedOut = app(EvolutionWhatsAppService::class)->logout();

        if ($loggedOut) {
            $this->state = 'close';
            $this->phone = null;
            $this->qrCode = app(EvolutionWhatsAppService::class)->connect();
            $this->audit('logout', 'close');
        }

        $notification = Notification::make()
            ->title($loggedOut ? 'WhatsApp disconnected' : 'Could not disconnect WhatsApp');

        ($loggedOut ? $notification->success() : $notification->danger())->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveTemplate')
                ->label('Save Message Template')
                ->icon('heroicon-o-check')
                ->action('saveTemplate'),
            Action::make('disconnect')
                ->label('Disconnect / Logout')
                ->icon('heroicon-o-power')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Disconnect WhatsApp?')
                ->modalDescription('This will stop outgoing WhatsApp order confirmations until the number is reconnected.')
                ->visible(fn (): bool => $this->state === 'open')
                ->action('disconnect'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    EmbeddedSchema::make('form'),
                ])
                    ->id('whatsapp-template-form')
                    ->livewireSubmitHandler('saveTemplate'),
            ]);
    }

    private function loadTemplates(): void
    {
        $setting = BuisnessSetting::query()->where('key', 'whatsapp-message')->first();

        $this->templateData = [];

        foreach (['en', 'ar'] as $locale) {
            $this->templateData[$locale]['message'] = data_get($setting?->translate($locale)?->value, 'message')
                ?? EvolutionWhatsAppService::DEFAULT_MESSAGE_TEMPLATES[$locale];
        }
    }

    private function templateTab(string $label, string $locale, bool $rtl): Tab
    {
        return Tab::make($label)->schema([
            Textarea::make($locale . '.message')
                ->label('Confirmation message (' . strtoupper($locale) . ')')
                ->rows(5)
                ->maxLength(500)
                ->required()
                ->helperText('Available tags: {customer_name}, {order_number}, {order_total}. Maximum 500 characters.')
                ->extraAttributes($rtl ? ['dir' => 'rtl', 'style' => 'text-align:right;'] : []),
        ]);
    }

    private function containsPlaceholder(string $template): bool
    {
        return str_contains($template, '{customer_name}')
            || str_contains($template, '{order_number}')
            || str_contains($template, '{order_total}');
    }

    private function audit(string $action, string $state): void
    {
        LogSecurityEventJob::dispatch([
            'user_id' => auth()->id(),
            'status_code' => $state === 'open' ? 200 : 503,
            'method' => 'ADMIN_ACTION',
            'url' => request()->fullUrl(),
            'path' => request()->path(),
            'ip' => request()->ip(),
            'country' => null,
            'device' => 'Admin Panel',
            'os' => null,
            'browser' => null,
            'message' => "WhatsApp connection action: {$action}; state: {$state}",
            'user_agent' => request()->userAgent(),
        ]);
    }
}