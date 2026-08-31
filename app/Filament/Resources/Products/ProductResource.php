<?php

namespace App\Filament\Resources\Products;

use App\Enums\AdminRole;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user && (
            $user->isSuperAdmin()
            || $user->hasExtraPermission('products_manage')
            || $user->hasExtraPermission('products_view')
            || $user->hasExtraPermission('products')
        );
    }

    public static function canManageProducts(): bool
    {
        $user = Auth::user();

        return $user && (
            $user->isSuperAdmin()
            || $user->hasExtraPermission('products_manage')
            || $user->hasExtraPermission('products')
        );
    }

    public static function canCreate(): bool
    {
        return static::canManageProducts();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canManageProducts();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canManageProducts();
    }

    public static function canDeleteAny(): bool
    {
        return static::canManageProducts();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
