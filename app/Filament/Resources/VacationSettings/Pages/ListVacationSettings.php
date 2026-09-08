<?php

namespace App\Filament\Resources\VacationSettings\Pages;

use App\Filament\Resources\VacationSettings\VacationSettingsResource;
use App\Models\BuisnessSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVacationSettings extends ListRecords
{
    protected static string $resource = VacationSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return BuisnessSetting::query()->where('key', 'vacation-settings')->exists()
            ? []
            : [CreateAction::make()];
    }
}
