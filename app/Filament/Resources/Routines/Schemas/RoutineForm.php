<?php

namespace App\Filament\Resources\Routines\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Tabs\Tab;

class RoutineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Tabs::make('Translations')
                 ->tabs([
                    Tab::make('English')
                    ->schema([
                                TextInput::make('en.title')
                                    ->label('Title (EN)')
                                    ->required()
                                    ->maxLength(255),

                                    RichEditor::make('en.description')
                                        ->label('Description (EN)')
                                        ->required()
                                        ->toolbarButtons([
                                            'blockquote', 'bold', 'bulletList', 'codeBlock',
                                            'h2', 'h3', 'italic', 'link', 'orderedList',
                                            'redo', 'strike', 'underline', 'undo',
                                        ]), 
                            ]),


                     Tab::make('Arabic')
                        ->schema([
                            TextInput::make('ar.title')
                                    ->label('Title (AR)')
                                    ->required()
                                    ->maxLength(255),

                                 RichEditor::make('ar.description')
                                        ->label('Description (Arabic)')
                                        ->required()
                                        ->toolbarButtons([
                                            'blockquote', 'bold', 'bulletList', 'codeBlock',
                                            'h2', 'h3', 'italic', 'link', 'orderedList',
                                            'redo', 'strike', 'underline', 'undo',
                                        ])
                                        ->extraAttributes(['dir' => 'rtl', 'style' => 'text-align:right;']),   
                        ])       
                 ])->columnSpanFull(),

                 FileUpload::make('image')
                    ->image()
                    ->imageEditor()
                    ->directory('routines')
                    ->visibility('public')
                    ->columnSpanFull(),
            ]);
    }
}
