<?php

namespace App\Filament\Resources\OrderSettings\Pages;

use App\Models\BuisnessSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\OrderSettings\OrderSettingsResource;

class ListOrderSettings extends ListRecords
{
    protected static string $resource = OrderSettingsResource::class;

    protected function getHeaderActions(): array
    {
        if(BuisnessSetting::query()->where('key' , 'order_settings')->exists()){
           return []; 
        }
        return [
            CreateAction::make(),
        ];
    }
}
