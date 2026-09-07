<?php

namespace App\Filament\Resources\SeoSettings\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;

class SeoSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Page')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'seo-page-home' => 'Home Page',
                        'seo-page-shop' => 'Shop Page',
                        'seo-page-contact' => 'Contact Page',
                        'seo-page-wholesale-sales' => 'Wholesale Sales Page',
                        'seo-page-terms' => 'Terms Page',
                        'seo-page-routines' => 'Routines Page',
                        'seo-page-packages' => 'Packages Page',
                        default => ucfirst(str_replace('-', ' ', $state ?? 'Unknown')),
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
