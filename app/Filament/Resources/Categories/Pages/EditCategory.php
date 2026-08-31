<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;
    
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
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract translations
        $translations = [];
        foreach (['en', 'ar'] as $locale) {
            if (isset($data[$locale])) {
                $translations[$locale] = $data[$locale];
                unset($data[$locale]);
            }
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
                $this->record->translateOrNew($locale)->fill($translation)->save();
            }
        }
    }
}
