<?php

namespace App\Filament\Resources\SeoSettings\Pages;

use App\Filament\Resources\SeoSettings\SeoSettingsResource;
use App\Models\BuisnessSetting;
use Filament\Resources\Pages\ListRecords;

class ListSeoSettings extends ListRecords
{
    protected static string $resource = SeoSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function mount(): void
    {
        parent::mount();
        
        // Create default record if it doesn't exist
        foreach (['home', 'shop', 'contact', 'wholesale-sales', 'terms', 'routines', 'packages'] as $page) {
            BuisnessSetting::firstOrCreate(['key' => 'seo-page-' . $page]);
        }
    }
}
