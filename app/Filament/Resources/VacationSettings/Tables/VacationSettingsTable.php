<?php

namespace App\Filament\Resources\VacationSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class VacationSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('enabled')
                    ->label('Active')
                    ->boolean()
                    ->state(fn ($record) => (bool) ($record->translate('en')?->value['enabled'] ?? false)),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
