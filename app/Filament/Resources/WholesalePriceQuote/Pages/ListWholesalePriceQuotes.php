<?php

namespace App\Filament\Resources\WholesalePriceQuote\Pages;

use App\Filament\Resources\WholesalePriceQuote\WholesalePriceQuoteResource;
use App\Models\BuisnessSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWholesalePriceQuotes extends ListRecords
{
    protected static string $resource = WholesalePriceQuoteResource::class;

    protected function getHeaderActions(): array
    {
        return BuisnessSetting::query()->where('key', 'wholesale-price-quote')->exists()
            ? []
            : [CreateAction::make()];
    }
}