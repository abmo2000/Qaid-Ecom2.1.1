<?php

namespace App\Filament\Resources\SeoSettings\Pages;

use App\Filament\Resources\SeoSettings\SeoSettingsResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSeoSettings extends EditRecord
{
    protected static string $resource = SeoSettingsResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load translations into form
        $data['en'] = $this->record->translate('en')?->toArray() ?? [];
        $data['ar'] = $this->record->translate('ar')?->toArray() ?? [];

        // Parse JSON values into fields
        foreach (['en', 'ar'] as $locale) {
            if (isset($data[$locale]['value']) && is_string($data[$locale]['value'])) {
                $decoded = json_decode($data[$locale]['value'], true);
                if (is_array($decoded)) {
                    $data[$locale] = array_merge($data[$locale], $decoded);
                }
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convert form data to JSON for translation storage
        foreach (['en', 'ar'] as $locale) {
            if (isset($data[$locale])) {
                $localeData = [
                    'meta_title' => $data[$locale]['meta_title'] ?? '',
                    'meta_description' => $data[$locale]['meta_description'] ?? '',
                    'meta_keywords' => $data[$locale]['meta_keywords'] ?? '',
                ];
                $data[$locale] = ['value' => json_encode($localeData)];
                unset($data[$locale . '_meta_title'], $data[$locale . '_meta_description'], $data[$locale . '_meta_keywords']);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Save translations
        $requestData = request()->validate([
            'data' => 'array',
        ])['data'] ?? [];

        foreach (['en', 'ar'] as $locale) {
            if (isset($requestData[$locale])) {
                $localeData = [
                    'meta_title' => $requestData[$locale]['meta_title'] ?? '',
                    'meta_description' => $requestData[$locale]['meta_description'] ?? '',
                    'meta_keywords' => $requestData[$locale]['meta_keywords'] ?? '',
                ];
                $this->record->translateOrNew($locale)->value = json_encode($localeData);
                $this->record->translateOrNew($locale)->save();
            }
        }

        Notification::make()
            ->title('SEO Settings saved successfully!')
            ->success()
            ->send();
    }
}
