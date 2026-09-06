<?php

namespace App\Filament\Resources\WholesalePriceQuote\Pages;

use App\Filament\Resources\WholesalePriceQuote\WholesalePriceQuoteResource;
use App\Models\BuisnessSetting;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class EditWholesalePriceQuote extends EditRecord
{
    protected static string $resource = WholesalePriceQuoteResource::class;

    protected function handleRecordUpdate($record, array $data): BuisnessSetting
    {
        $newPath = Arr::wrap($data['file_path'] ?? [])[0] ?? null;

        if ($newPath && $record->file_path && $record->file_path !== $newPath) {
            Storage::disk('public')->delete($record->file_path);
        }

        $record->file_path = $newPath;
        $record->save();

        return $record;
    }
}