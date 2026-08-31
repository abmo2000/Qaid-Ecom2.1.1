<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs\Tab;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->imageEditor()
                    ->directory('categories')
                    ->visibility('public')
                    ->columnSpanFull(),

                     Tabs::make('Translations')
                 ->tabs([
                    Tab::make('English')
                    ->schema([
                                TextInput::make('en.title')
                                    ->label('Title (EN)')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                     Tab::make('Arabic')
                        ->schema([
                            TextInput::make('ar.title')
                                    ->label('Title (AR)')
                                    ->required()
                                    ->maxLength(255),
                        ])       
                 ])->columnSpanFull(),

            ]);
    }
}
