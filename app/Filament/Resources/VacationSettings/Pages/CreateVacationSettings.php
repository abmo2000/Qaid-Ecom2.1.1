<?php

namespace App\Filament\Resources\VacationSettings\Pages;

use App\Filament\Resources\VacationSettings\VacationSettingsResource;
use App\Models\BuisnessSetting;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateVacationSettings extends CreateRecord
{
    protected static string $resource = VacationSettingsResource::class;

    protected array $translations = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->translations = [
            'en' => [
                'enabled' => (bool) ($data['enabled'] ?? false),
                'message' => $data['en']['message'] ?? '',
            ],
            'ar' => [
                'enabled' => (bool) ($data['enabled'] ?? false),
                'message' => $data['ar']['message'] ?? '',
            ],
        ];

        return [];
    }

    protected function handleRecordCreation(array $data): Model
    {
        return BuisnessSetting::create(['key' => 'vacation-settings']);
    }

    protected function afterCreate(): void
    {
        foreach ($this->translations as $locale => $translation) {
            $this->record->translations()->updateOrCreate(
                ['buisness_setting_id' => $this->record->id, 'locale' => $locale],
                ['value' => $translation],
            );
        }
    }
}
