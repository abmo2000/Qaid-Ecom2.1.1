<?php

namespace App\Filament\Resources\VacationSettings;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\BuisnessSetting;
use App\Filament\Resources\VacationSettings\Pages\EditVacationSettings;
use App\Filament\Resources\VacationSettings\Pages\ListVacationSettings;
use App\Filament\Resources\VacationSettings\Pages\CreateVacationSettings;
use App\Filament\Resources\VacationSettings\Schemas\VacationSettingsForm;
use App\Filament\Resources\VacationSettings\Tables\VacationSettingsTable;
use UnitEnum;

class VacationSettingsResource extends Resource
{
    protected static ?string $model = BuisnessSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMoon;

    protected static ?string $navigationLabel = 'Vacation Mode';

    protected static string|UnitEnum|null $navigationGroup = 'Business Settings';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->isSuperAdmin() || $user->hasExtraPermission('vacation_settings'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return VacationSettingsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VacationSettingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVacationSettings::route('/'),
            'create' => CreateVacationSettings::route('/create'),
            'edit' => EditVacationSettings::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('key', 'vacation-settings');
    }
}
