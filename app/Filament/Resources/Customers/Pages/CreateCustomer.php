<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected ?string $plainPassword = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->plainPassword = $data['password'] ?? null;
        $data['role_name'] = 'customer';

        return $data;
    }

    protected function afterCreate(): void
    {
        if (! filled($this->plainPassword)) {
            return;
        }

        Notification::make()
            ->title('Customer created')
            ->body("Customer password: {$this->plainPassword}")
            ->success()
            ->persistent()
            ->send();
    }
}
