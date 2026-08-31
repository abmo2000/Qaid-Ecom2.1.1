<?php

namespace App\Filament\Resources\ContentManagement\Pages;

use App\Filament\Resources\ContentManagement\ContentManagementResource;
use App\Models\BuisnessSetting;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateContentManagement extends CreateRecord
{
    protected static string $resource = ContentManagementResource::class;


     protected array $translations = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract translations
        $translations = [];
        foreach (['en', 'ar'] as $locale) {
            if (isset($data[$locale])) {
                $translations[$locale] = $data[$locale];
                unset($data[$locale]);
            }
        }
        
        // Store translations separately to be handled after creation
        $this->translations = $translations;
    
        return $data;
    }


    protected function handleRecordCreation(array $data): Model
    {
         return BuisnessSetting::create(['key' => 'content-management']);
    }
    
    protected function afterCreate(): void
    {
        // Save translations
        if (isset($this->translations)) {
            foreach ($this->translations as $locale => $translation) {
                $this->record->translateOrNew($locale)->updateOrCreate([
                    'buisness_setting_id' => $this->record->id,
                    'locale' => $locale,
                ], [
                    'value' => json_encode($translation)
                ]);
            }
        }
    }
}
