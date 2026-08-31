<?php

namespace App\Filament\Auth;

use App\Enums\AdminRole;
use App\Models\User;
use Filament\Auth\Pages\Register;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class AdminRegister extends Register
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getRoleFormComponent(),
            ]);
    }

    protected function getRoleFormComponent(): Component
    {
        return Select::make('role_name')
            ->label('Role')
            ->options(
                collect(AdminRole::registrableRoles())
                    ->mapWithKeys(fn(AdminRole $role) => [$role->value => $role->label()])
                    ->toArray()
            )
            ->required();
    }

    protected function handleRegistration(array $data): Model
    {
        $data['is_approved'] = false;

        return $this->getUserModel()::create($data);
    }

    public function register(): null
    {
        try {
            $this->rateLimit(2);
        } catch (\DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function (): Model {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        Notification::make()
            ->title('Registration submitted!')
            ->body('Your account is pending approval by the Super Admin. You will be able to log in once approved.')
            ->success()
            ->send();

        $this->form->fill();

        return null;
    }

    public function getTitle(): string|Htmlable
    {
        return 'Admin Registration';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Admin Registration';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString('Register for an admin account (requires Super Admin approval)<br><br>' . $this->loginAction->toHtml());
    }
}
