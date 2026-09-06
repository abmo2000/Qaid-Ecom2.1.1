<?php

namespace App\Filament\Resources\WholesalePriceQuote;

use App\Filament\Resources\WholesalePriceQuote\Pages\CreateWholesalePriceQuote;
use App\Filament\Resources\WholesalePriceQuote\Pages\EditWholesalePriceQuote;
use App\Filament\Resources\WholesalePriceQuote\Pages\ListWholesalePriceQuotes;
use App\Filament\Resources\WholesalePriceQuote\Schemas\WholesalePriceQuoteForm;
use App\Filament\Resources\WholesalePriceQuote\Tables\WholesalePriceQuoteTable;
use App\Models\BuisnessSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class WholesalePriceQuoteResource extends Resource
{
    protected static ?string $model = BuisnessSetting::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowDown;
    protected static ?string $navigationLabel = 'Wholesale Price Quote';
    protected static string|UnitEnum|null $navigationGroup = 'Business Settings';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->isSuperAdmin() || $user->hasExtraPermission('business_info'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return WholesalePriceQuoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WholesalePriceQuoteTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWholesalePriceQuotes::route('/'),
            'create' => CreateWholesalePriceQuote::route('/create'),
            'edit' => EditWholesalePriceQuote::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('key', 'wholesale-price-quote');
    }
}