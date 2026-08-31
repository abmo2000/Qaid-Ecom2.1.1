<?php

namespace App\Filament\Resources\AdminUsers\Pages;

use App\Filament\Resources\AdminUsers\AdminUserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAdminUser extends CreateRecord
{
    protected static string $resource = AdminUserResource::class;

    protected ?string $plainPassword = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->plainPassword = $data['password'] ?? null;

        return AdminUserResource::mergePermissions($data);
    }

    protected function afterCreate(): void
    {
        if (! filled($this->plainPassword)) {
            return;
        }

        Notification::make()
            ->title('Admin user created')
            ->body("The password is: {$this->plainPassword}. Please share it with the new admin and ask them to change it after first login.")
            ->success()
            ->persistent()
            ->send();
    }
}
