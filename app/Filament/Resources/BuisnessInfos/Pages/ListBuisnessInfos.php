<?php

namespace App\Filament\Resources\BuisnessInfos\Pages;

use App\Filament\Resources\BuisnessInfos\BuisnessInfoResource;
use App\Models\BuisnessSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBuisnessInfos extends ListRecords
{
    protected static string $resource = BuisnessInfoResource::class;

    protected function getHeaderActions(): array
    {
        if(BuisnessSetting::query()->where('key' , 'buisness-info')->exists()){
              return [];
        }

            return [

            CreateAction::make(),
        ];
      
    }

}
