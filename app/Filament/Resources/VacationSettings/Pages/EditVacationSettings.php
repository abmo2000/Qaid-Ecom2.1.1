<?php

namespace App\Filament\Resources\VacationSettings\Pages;

use App\Filament\Resources\VacationSettings\VacationSettingsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVacationSettings extends EditRecord
{
    protected static string $resource = VacationSettingsResource::class;

    protected array $translations = [];

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $english = $this->record->translate('en')?->value ?? [];
        $arabic = $this->record->translate('ar')?->value ?? [];

        return [
            'enabled' => (bool) ($english['enabled'] ?? $arabic['enabled'] ?? false),
            'en' => ['message' => $english['message'] ?? ''],
            'ar' => ['message' => $arabic['message'] ?? ''],
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function afterSave(): void
    {
        foreach ($this->translations as $locale => $translation) {
            $this->record->translations()->updateOrCreate(
                ['buisness_setting_id' => $this->record->id, 'locale' => $locale],
                ['value' => $translation],
            );
        }
    }
}
