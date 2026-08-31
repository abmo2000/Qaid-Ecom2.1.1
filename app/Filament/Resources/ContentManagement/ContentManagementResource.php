<?php

namespace App\Filament\Resources\ContentManagement;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\BuisnessSetting;
use Filament\Resources\Resource;
use App\Models\ContentManagement;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ContentManagement\Pages\EditContentManagement;
use App\Filament\Resources\ContentManagement\Pages\ListContentManagement;
use App\Filament\Resources\ContentManagement\Pages\CreateContentManagement;
use App\Filament\Resources\ContentManagement\Schemas\ContentManagementForm;
use App\Filament\Resources\ContentManagement\Tables\ContentManagementTable;

class ContentManagementResource extends Resource
{
    protected static ?string $model = BuisnessSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->isSuperAdmin() || $user->hasExtraPermission('content_management'));
    }

    public static function form(Schema $schema): Schema
    {
        return ContentManagementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentManagementTable::configure($table);
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
            'index' => ListContentManagement::route('/'),
            'create' => CreateContentManagement::route('/create'),
            'edit' => EditContentManagement::route('/{record}/edit'),
        ];
    }

     public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

     public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('key', 'content-management');
    }
}
