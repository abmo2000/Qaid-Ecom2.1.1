<?php

namespace App\Filament\Resources\BuisnessInfos\Pages;

use App\Models\BuisnessSetting;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\BuisnessInfos\BuisnessInfoResource;

class CreateBuisnessInfo extends CreateRecord
{
    protected static string $resource = BuisnessInfoResource::class;

      protected array $translations = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        
        // Extract translations
        $translations = [];
        foreach (['en', 'ar'] as $locale) {
                $translations[$locale] = $data;
        }
        
        // Store translations separately to be handled after creation
        $this->translations = $translations;
    
        return $data;
    }


    protected function handleRecordCreation(array $data): Model
    {
         return BuisnessSetting::create(['key' => 'buisness-info']);
    }
    
    protected function afterCreate(): void
    {
    
     
        // Save translations
        if (isset($this->translations)) {
            foreach ($this->translations as $locale => $translation) {
                $this->record->translations()->updateOrCreate([
                    'buisness_setting_id' => $this->record->id,
                    'locale' => $locale,
                ], [
                    'value' => json_encode($translation)
                ]);
            }
        }
    }
}
