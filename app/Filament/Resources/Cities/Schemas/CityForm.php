<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Translations')
                    ->tabs([
                        Tab::make('English')
                            ->schema([
                                TextInput::make('en.name')
                                    ->label('Government Name (EN)')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Tab::make('Arabic')
                            ->schema([
                                TextInput::make('ar.name')
                                    ->label('Government Name (AR)')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ])
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label('Delivery Price (EGP)')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->columnSpanFull(),

                Toggle::make('has_discussion_for_delivery')
                    ->label('Allow discuss delivery price')
                    ->helperText('Enable if the customer can choose "discuss" delivery option for this government.')
                    ->columnSpanFull(),
            ]);
    }
}
