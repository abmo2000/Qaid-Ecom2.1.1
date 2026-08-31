<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Coupons\Pages\ListCoupons;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Package;
use App\Models\Product;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = 'Coupons';

    protected static ?string $modelLabel = 'Coupon';

    protected static ?string $pluralModelLabel = 'Coupons';

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && ($user->isSuperAdmin() || $user->hasExtraPermission('coupons'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('code')
                ->required()
                ->maxLength(64)
                ->unique(ignoreRecord: true)
                ->dehydrateStateUsing(fn (?string $state): string => strtoupper(trim((string) $state))),

            TextInput::make('discount_percentage')
                ->label('Discount %')
                ->numeric()
                ->required()
                ->minValue(1)
                ->maxValue(100)
                ->suffix('%'),

            Select::make('scopeable_type')
                ->label('Applies To Type')
                ->nullable()
                ->placeholder('All products (global)')
                ->options([
                    'product' => 'Product',
                    'category' => 'Category',
                    'package' => 'Package',
                ])
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('scopeable_id', null)),

            Select::make('scopeable_id')
                ->label('Applies To')
                ->nullable()
                ->searchable()
                ->hidden(fn (Get $get): bool => blank($get('scopeable_type')))
                ->options(function (Get $get): array {
                    return match ($get('scopeable_type')) {
                        'product' => Product::query()
                            ->with('translations')
                            ->get()
                            ->mapWithKeys(fn (Product $product): array => [$product->id => $product->name ?? ('Product #' . $product->id)])
                            ->toArray(),
                        'category' => Category::query()
                            ->with('translations')
                            ->get()
                            ->mapWithKeys(fn (Category $category): array => [$category->id => $category->title ?? ('Category #' . $category->id)])
                            ->toArray(),
                        'package' => Package::query()
                            ->with('translations')
                            ->get()
                            ->mapWithKeys(fn (Package $package): array => [$package->id => $package->name ?? ('Package #' . $package->id)])
                            ->toArray(),
                        default => [],
                    };
                }),

            DateTimePicker::make('starts_at')
                ->label('Start Date')
                ->seconds(false),

            DateTimePicker::make('expires_at')
                ->label('Expiry Date')
                ->seconds(false)
                ->after('starts_at'),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();

        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('discount_percentage')
                    ->label('Discount')
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('scopeable_type')
                    ->label('Target Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => ucfirst((string) $state)),

                TextColumn::make('scopeable_name')
                    ->label('Target')
                    ->placeholder('N/A')
                    ->searchable(),

                TextColumn::make('orders_count')
                    ->label('Used Count')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable()
                    ->visible($user instanceof User && $user->isSuperAdmin()),

                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expiry Date')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('No expiry'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('discount_percentage', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['creator', 'scopeable'])
            ->withCount('orders');
        $user = Auth::user();

        if ($user instanceof User && $user->isSalesAdmin()) {
            return $query->where('created_by', $user->id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'edit' => EditCoupon::route('/{record}/edit'),
        ];
    }
}
