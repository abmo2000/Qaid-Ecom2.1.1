<?php

namespace App\Filament\Resources\SeoSettings\Pages;

use App\Filament\Resources\SeoSettings\SeoSettingsResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSeoSettings extends EditRecord
{
    protected static string $resource = SeoSettingsResource::class;

    protected array $translations = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['en'] = $this->record->translate('en')?->toArray() ?? [];
        $data['ar'] = $this->record->translate('ar')?->toArray() ?? [];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        foreach (['en', 'ar'] as $locale) {
            if (isset($data[$locale])) {
                $this->translations[$locale] = $data[$locale];
                unset($data[$locale]);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        foreach ($this->translations as $locale => $translation) {
            $translation['value'] = $this->record->translate($locale)?->value ?? [];
            $this->record->translateOrNew($locale)->fill($translation)->save();
        }

        Notification::make()
            ->title('SEO Settings saved successfully!')
            ->success()
            ->send();
    }
}
