<?php

namespace App\Filament\Resources\Cities\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Government')
                    ->searchable(true, function ($query, $search) {
                        return $query->whereHas('translations', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('price')
                    ->label('Delivery Fee')
                    ->money('EGP')
                    ->sortable(),

                IconColumn::make('has_discussion_for_delivery')
                    ->label('Discuss Delivery')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                // Add filters if needed.
            ])
            ->defaultSort('created_at', 'desc');
    }
}
