<?php

namespace App\Filament\Resources\ContentManagement\Pages;

use App\Filament\Resources\ContentManagement\ContentManagementResource;
use App\Models\BuisnessSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentManagement extends ListRecords
{
    protected static string $resource = ContentManagementResource::class;

    protected function getHeaderActions(): array
    {
        if(BuisnessSetting::query()->where('key' , 'content-management')->exists()){
           return []; 
        }
        return [
            CreateAction::make(),
        ];
    }
}
