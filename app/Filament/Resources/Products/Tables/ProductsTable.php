<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use App\Models\Routine;
use App\Models\Category;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Collection;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                   ImageColumn::make('image')
                    ->circular(),

                TextColumn::make('name')
                    ->searchable(  true,function ($query, $search) {
                        return $query->whereHas('translations', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('brand')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('is_published')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?bool $state): string => $state ? 'Published' : 'Pending')
                    ->colors([
                        'danger' => static fn (?bool $state): bool => ! $state,
                        'success' => static fn (?bool $state): bool => $state,
                    ])
                    ->sortable(),

                TextColumn::make('import_error')
                    ->label('Import issue')
                    ->wrap()
                    ->limit(40)
                    ->visible(fn (): bool => ProductResource::canManageProducts()),

                   TextInputColumn::make('price')
                   ->sortable()
                   ->rules(['required', 'numeric', 'min:1'])
                   ->visible(fn (): bool => ProductResource::canManageProducts()),

                   TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->visible(fn (): bool => ProductResource::canManageProducts()),

                TextColumn::make('price')
                    ->label('Price')
                    ->sortable()
                    ->visible(fn (): bool => ! ProductResource::canManageProducts()),
                   
                   ToggleColumn::make('featured')
                    ->label('Featured')
                    ->onIcon('heroicon-s-star')
                    ->offIcon('heroicon-o-star')
                    ->onColor('warning')
                    ->offColor('secondary')
                    ->visible(fn (): bool => ProductResource::canManageProducts()),

                   ToggleColumn::make('in_stock')
                    ->visible(fn (): bool => ProductResource::canManageProducts()),

                IconColumn::make('in_stock')
                    ->label('In stock')
                    ->boolean()
                    ->visible(fn (): bool => ! ProductResource::canManageProducts()),

            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->options(Category::query()->with(['translations'])->get()->pluck('title' , 'id'))
                    ->attribute('category_id'),

                 SelectFilter::make('Routine')
                    ->label('Rotine')
                    ->options(Routine::query()->with(['translations'])->get()->pluck('title' , 'id'))
                    ->attribute('routine_id'),

                 TernaryFilter::make('in_stock'),

                 SelectFilter::make('published')
                    ->label('Published')
                    ->options([
                        0 => 'Pending',
                        1 => 'Published',
                    ])
                    ->attribute('is_published'),

                 TernaryFilter::make('featured'),
              TernaryFilter::make('has_sale')
                ->label('Has Sale')
                ->queries(
                    true: fn (Builder $query) => $query->whereHas('sale'),
                    false: fn (Builder $query) => $query->whereDoesntHave('sale'),
                    blank: fn (Builder $query) => $query,
                ),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool => ProductResource::canManageProducts()),
                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn (Product $record): bool => ProductResource::canManageProducts() && ! $record->is_published)
                    ->action(function (Product $record) {
                        $record->update(['is_published' => true]);

                        Notification::make()
                            ->title('Product published')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make()
                    ->visible(fn (): bool => ProductResource::canManageProducts())
            ])
            
            ->toolbarActions([
                BulkActionGroup::make([

            BulkAction::make('add_sale')
            ->label('Add Sale')
            ->icon('heroicon-o-tag')
            ->color('success')
            ->modalHeading('Add Sale to Selected Products')
            ->modalSubmitActionLabel('Apply Sale')
            ->schema([
                TextInput::make('sale_price')
                    ->label('Sale Price')
                    ->numeric()
                    ->required()
                    ->minValue(1),
            ])
            ->action(function (Collection $records, array $data) {
                foreach ($records as $product) {
                    $product->sale()->updateOrCreate(
                        [],
                        [
                            'sale_price' => $data['sale_price'],
                        ]
                    );
                }

                Notification::make()
                    ->title('Sale applied successfully')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion()
            ->visible(fn (): bool => ProductResource::canManageProducts()),

            BulkAction::make('remove_sale')
              ->label('Remove Sale')
              ->icon('heroicon-o-x-mark')
              ->color('danger')
              ->action(function(Collection $records , array $data){
                foreach ($records as $product) {
                    $product->sale()->delete();
                }

                Notification::make()
                    ->title('Sale removed successfully')
                    ->success()
                    ->send();
              })->deselectRecordsAfterCompletion()
                ->visible(fn (): bool => ProductResource::canManageProducts()),

            BulkAction::make('publish')
                ->label('Publish Selected')
                ->icon('heroicon-o-check-circle')
                ->action(function (Collection $records) {
                    foreach ($records as $product) {
                        $product->update(['is_published' => true]);
                    }

                    Notification::make()
                        ->title('Selected products published')
                        ->success()
                        ->send();
                })
                ->deselectRecordsAfterCompletion()
                ->visible(fn (): bool => ProductResource::canManageProducts()),
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => ProductResource::canManageProducts()),
                ]),
            ]);
    }
}
