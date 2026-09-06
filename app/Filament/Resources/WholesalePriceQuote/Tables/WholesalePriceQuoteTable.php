<?php

namespace App\Filament\Resources\WholesalePriceQuote\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WholesalePriceQuoteTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('file_path')->label('Uploaded PDF'),
            TextColumn::make('updated_at')->dateTime()->label('Last Updated'),
        ])->recordActions([
            EditAction::make(),
        ]);
    }
}