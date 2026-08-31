<?php

namespace App\Filament\Resources\TermsSettings;

use BackedEnum;
use Filament\Tables\Table;
use App\Models\BuisnessSetting;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TermsSettings\Pages\EditTermsSettings;
use App\Filament\Resources\TermsSettings\Pages\ListTermsSettings;
use App\Filament\Resources\TermsSettings\Pages\CreateTermsSettings;
use App\Filament\Resources\TermsSettings\Schemas\TermsSettingsForm;
use App\Filament\Resources\TermsSettings\Tables\TermsSettingsTable;
use UnitEnum;

class TermsSettingsResource extends Resource
{
    protected static ?string $model = BuisnessSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Terms & Conditions';

    protected static string|UnitEnum|null $navigationGroup = 'Business Settings';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->isSuperAdmin() || $user->hasExtraPermission('business_info'));
    }

    public static function form(Schema $schema): Schema
    {
        return TermsSettingsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TermsSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTermsSettings::route('/'),
            'create' => CreateTermsSettings::route('/create'),
            'edit' => EditTermsSettings::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('key', 'terms');
    }
}
