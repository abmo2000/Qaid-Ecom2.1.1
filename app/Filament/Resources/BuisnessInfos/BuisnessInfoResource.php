<?php

namespace App\Filament\Resources\BuisnessInfos;

use BackedEnum;
use Filament\Tables\Table;
use App\Models\BuisnessInfo;
use Filament\Schemas\Schema;
use App\Models\BuisnessSetting;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BuisnessInfos\Pages\EditBuisnessInfo;
use App\Filament\Resources\BuisnessInfos\Pages\ListBuisnessInfos;
use App\Filament\Resources\BuisnessInfos\Pages\CreateBuisnessInfo;
use App\Filament\Resources\BuisnessInfos\Schemas\BuisnessInfoForm;
use App\Filament\Resources\BuisnessInfos\Tables\BuisnessInfosTable;
use UnitEnum;

class BuisnessInfoResource extends Resource
{
    protected static ?string $model = BuisnessSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $navigationLabel = 'Business Info';

    protected static string|UnitEnum|null $navigationGroup = 'Business Settings';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->isSuperAdmin() || $user->hasExtraPermission('business_info'));
    }

    public static function form(Schema $schema): Schema
    {
        return BuisnessInfoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BuisnessInfosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBuisnessInfos::route('/'),
            'create' => CreateBuisnessInfo::route('/create'),
            'edit' => EditBuisnessInfo::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('key', 'buisness-info');
    }
}
