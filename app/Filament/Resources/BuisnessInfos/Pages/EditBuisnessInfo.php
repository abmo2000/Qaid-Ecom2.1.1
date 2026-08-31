<?php

namespace App\Filament\Resources\BuisnessInfos\Pages;

use Illuminate\Support\Arr;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\BuisnessInfos\BuisnessInfoResource;

class EditBuisnessInfo extends EditRecord
{
    protected static string $resource = BuisnessInfoResource::class;

  protected array $translations = [];
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

         protected function mutateFormDataBeforeFill(array $data): array
    {
       
        // Load translations into form
        $data['en'] = $this->record->translate('en')?->toArray() ?? [];
        $data['ar'] = $this->record->translate('ar')?->toArray() ?? [];
         return Arr::only($data , ['en' , 'ar']); 
        
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract translations
        $translations = [];
        foreach (['en', 'ar'] as $locale) {
                $translations[$locale] = $data;
               
        
        }
        
        // Store translations separately
        $this->translations = $translations;
        
        return $data;
    }
    
    protected function afterSave(): void
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
