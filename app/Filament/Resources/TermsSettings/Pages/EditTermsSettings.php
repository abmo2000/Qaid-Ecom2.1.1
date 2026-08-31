<?php

namespace App\Filament\Resources\TermsSettings\Pages;

use Illuminate\Support\Arr;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TermsSettings\TermsSettingsResource;

class EditTermsSettings extends EditRecord
{
    protected static string $resource = TermsSettingsResource::class;

    protected array $translations = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['en'] = $this->record->translate('en')?->toArray() ?? [];
        $data['ar'] = $this->record->translate('ar')?->toArray() ?? [];

        foreach (['en', 'ar'] as $locale) {
            if (! empty($data[$locale]['value'])) {
                $value = $data[$locale]['value'];
                if (is_string($value)) {
                    $value = json_decode($value, true) ?: [];
                }

                $data[$locale] = [
                    'terms' => $value['terms'] ?? '',
                ];
            }
        }

        return Arr::only($data, ['en', 'ar']);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->translations = [
            'en' => ['terms' => $data['en']['terms'] ?? ''],
            'ar' => ['terms' => $data['ar']['terms'] ?? ''],
        ];

        return $data;
    }

    protected function afterSave(): void
    {
        if (isset($this->translations)) {
            foreach ($this->translations as $locale => $translation) {
                $this->record->translateOrNew($locale)->value = json_encode($translation);
                $this->record->translateOrNew($locale)->save();
            }
        }
    }
}
