<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Filament\Resources\Packages\PackagesResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePackages extends CreateRecord
{
    protected static string $resource = PackagesResource::class;


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
    
    protected function afterCreate(): void
    {
        // Save translations
        if (isset($this->translations)) {
            foreach ($this->translations as $locale => $translation) {
                $this->record->translateOrNew($locale)->fill($translation)->save();
            }
        }

        $products = $this->form->getState()['products'] ?? [];
        $this->record->products()->sync($products);

        $this->record->recalculateOriginalPrice();
    }


 
}
