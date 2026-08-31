<?php

namespace App\Filament\Resources\SalesSummary;

use App\Filament\Resources\SalesSummary\Pages\ListSalesSummary;
use App\Filament\Resources\SalesSummary\Tables\SalesSummaryTable;
use App\Models\Order;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SalesSummaryResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Sales Summary';

    protected static ?string $modelLabel = 'Sales Summary';

    protected static ?string $pluralModelLabel = 'Sales Summary';

    protected static ?string $slug = 'sales-summary';

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isSuperAdmin();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function table(Table $table): Table
    {
        return SalesSummaryTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalesSummary::route('/'),
        ];
    }
}
