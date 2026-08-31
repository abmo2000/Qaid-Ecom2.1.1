<?php

namespace App\Filament\Pages;

use App\Models\SmtpSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use UnitEnum;

class ManageSmtpSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'SMTP Settings';

    protected static ?string $title = 'SMTP Settings';

    protected static string|UnitEnum|null $navigationGroup = 'Business Settings';

    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?array $data = [];

    public ?string $test_email = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->isSuperAdmin();
    }

    public function mount(): void
    {
        $smtp = SmtpSetting::first();

        $this->form->fill($smtp ? $smtp->toArray() : [
            'driver' => 'smtp',
            'port' => 587,
            'encryption' => 'tls',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('host')
                    ->label('SMTP Host')
                    ->placeholder('smtp.gmail.com')
                    ->required(),

                TextInput::make('port')
                    ->label('SMTP Port')
                    ->numeric()
                    ->default(587)
                    ->required(),

                Select::make('encryption')
                    ->label('Encryption')
                    ->options([
                        'tls' => 'TLS',
                        'ssl' => 'SSL',
                        '' => 'None',
                    ])
                    ->default('tls')
                    ->required(),

                TextInput::make('username')
                    ->label('SMTP Username')
                    ->placeholder('your@email.com')
                    ->required(),

                TextInput::make('password')
                    ->label('SMTP Password')
                    ->password()
                    ->revealable()
                    ->required(),

                TextInput::make('from_address')
                    ->label('From Address')
                    ->email()
                    ->placeholder('noreply@example.com')
                    ->required(),

                TextInput::make('from_name')
                    ->label('From Name')
                    ->placeholder('My App')
                    ->required(),

                Select::make('driver')
                    ->label('Mail Driver')
                    ->options([
                        'smtp' => 'SMTP',
                        'sendmail' => 'Sendmail',
                        'mailgun' => 'Mailgun',
                    ])
                    ->default('smtp')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SmtpSetting::updateOrCreate(
            ['id' => 1],
            $data,
        );

        Notification::make()
            ->title('SMTP settings saved successfully')
            ->success()
            ->send();
    }

    public function sendTestEmail(): void
    {
        $validated = $this->validate([
            'test_email' => ['required', 'email'],
        ]);

        try {
            Mail::raw('This is a test email from your application. SMTP settings are working correctly!', function ($message) use ($validated) {
                $message->to($validated['test_email'])
                    ->subject('SMTP Test Email');
            });

            Notification::make()
                ->title('Test email sent successfully!')
                ->body("A test email was sent to {$validated['test_email']}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to send test email')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save')
                ->icon('heroicon-o-check'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Form::make([
                    \Filament\Schemas\Components\EmbeddedSchema::make('form'),
                ])
                    ->id('form')
                    ->livewireSubmitHandler('save'),

                \Filament\Schemas\Components\Section::make('Test Email')
                    ->description('Send a test email to verify your SMTP configuration')
                    ->schema([
                        TextInput::make('test_email')
                            ->label('Test Email Address')
                            ->email()
                            ->placeholder('test@example.com')
                            ->statePath('test_email'),

                        \Filament\Schemas\Components\Actions::make([
                            Action::make('send_test')
                                ->label('Send Test Email')
                                ->icon('heroicon-o-paper-airplane')
                                ->action('sendTestEmail'),
                        ]),
                    ]),
            ]);
    }
}
