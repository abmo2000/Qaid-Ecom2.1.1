<?php

namespace App\Filament\Resources\WholesalePriceQuote\Pages;

use App\Filament\Resources\WholesalePriceQuote\WholesalePriceQuoteResource;
use App\Models\BuisnessSetting;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateWholesalePriceQuote extends CreateRecord
{
    protected static string $resource = WholesalePriceQuoteResource::class;

    protected function handleRecordCreation(array $data): BuisnessSetting
    {
        return BuisnessSetting::create([
            'key' => 'wholesale-price-quote',
            'file_path' => Arr::wrap($data['file_path'] ?? [])[0] ?? null,
        ]);
    }
}