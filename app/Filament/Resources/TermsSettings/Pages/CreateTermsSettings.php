<?php

namespace App\Filament\Resources\TermsSettings\Pages;

use App\Models\BuisnessSetting;
use App\Filament\Resources\TermsSettings\TermsSettingsResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTermsSettings extends CreateRecord
{
    protected static string $resource = TermsSettingsResource::class;

    protected array $translations = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->translations = [
            'en' => $data['en'] ?? [],
            'ar' => $data['ar'] ?? [],
        ];

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return BuisnessSetting::create(['key' => 'terms']);
    }

    protected function afterCreate(): void
    {
        if (isset($this->translations)) {
            foreach ($this->translations as $locale => $translation) {
                $this->record->translateOrNew($locale)->value = json_encode($translation);
                $this->record->translateOrNew($locale)->save();
            }
        }
    }
}
