<?php

namespace App\Filament\Resources\SeoSettings\Pages;

use App\Filament\Resources\SeoSettings\SeoSettingsResource;
use App\Models\BuisnessSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSeoSettings extends ListRecords
{
    protected static string $resource = SeoSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(function (array $data) {
                    return BuisnessSetting::create([
                        'key' => 'seo-settings',
                    ]);
                }),
        ];
    }

    public function mount(): void
    {
        parent::mount();
        
        // Create default record if it doesn't exist
        if (!BuisnessSetting::where('key', 'seo-settings')->exists()) {
            BuisnessSetting::create(['key' => 'seo-settings']);
        }
    }
}
