<?php

namespace App\Filament\Resources\TermsSettings\Pages;

use App\Filament\Resources\TermsSettings\TermsSettingsResource;
use App\Models\BuisnessSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTermsSettings extends ListRecords
{
    protected static string $resource = TermsSettingsResource::class;

    protected function getHeaderActions(): array
    {
        if (BuisnessSetting::query()->where('key', 'terms')->exists()) {
            return [];
        }

        return [
            CreateAction::make(),
        ];
    }
}
