<?php

namespace App\Filament\Resources\Packages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('packages.table.name'))
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->whereHas('translations', fn (Builder $translations): Builder => $translations
                            ->whereIn('locale', ['en', 'ar'])
                            ->where('name', 'like', "%{$search}%"))),
                TextColumn::make('price')
                    ->label(__('packages.form.price'))
                    ->sortable(),
                TextColumn::make('products_count')
                    ->label(__('packages.table.products_count'))
                    ->counts('products'),
                ToggleColumn::make('is_active')
                    ->label(__('packages.form.is_active')),
                TextColumn::make('created_at')
                    ->label(__('packages.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('packages.table.status'))
                    ->placeholder(__('packages.table.any_status'))
                    ->trueLabel(__('packages.table.active'))
                    ->falseLabel(__('packages.table.inactive')),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
