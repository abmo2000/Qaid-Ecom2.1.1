<?php

namespace App\Filament\Resources\WholesalePriceQuote\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WholesalePriceQuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Price Quote PDF')->schema([
                FileUpload::make('file_path')
                    ->label('Price Quote PDF')
                    ->acceptedFileTypes(['application/pdf'])
                    ->disk('public')
                    ->directory('price-quotes')
                    ->visibility('public')
                    ->required(),
            ]),
        ]);
    }
}