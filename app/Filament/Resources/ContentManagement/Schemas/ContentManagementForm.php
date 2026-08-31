<?php

namespace App\Filament\Resources\ContentManagement\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Tabs\Tab;

class ContentManagementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Our Concepts")
                ->schema([
                    Tabs::make('Translations')
                        ->tabs([
                            Tab::make('English')
                                ->schema([

                                    RichEditor::make('en.description')
                                        ->label('Description (EN)')
                                        ->required()
                                        ->afterStateHydrated(function ($state, callable $set, $record) {
                                              if($record){

                                                  $arTranslation = $record->translations()->where('locale', 'en')->first();
           
                                                   if ($arTranslation?->value) {
                                                       $decodedValue = json_decode($arTranslation->value, true);
                                                       $description = $decodedValue['description'] ?? '<p></p>';
                                                       $set('en.description', $description ?: '<p></p>');
                                                   } else {
                                                       $set('en.description', '<p></p>');
                                                   }
                                              }
                                          })
                                        ->toolbarButtons([
                                            'blockquote', 'bold', 'bulletList', 'codeBlock',
                                            'h2', 'h3', 'italic', 'link', 'orderedList',
                                            'redo', 'strike', 'underline', 'undo',
                                        ]),
                                ]),

                            Tab::make('Arabic')
                                ->schema([
                                    RichEditor::make('ar.description')
                                        ->label('Description (AR)')
                                        ->required()
                                        ->extraAttributes(['dir' => 'rtl'])
                                        ->afterStateHydrated(function ($state, callable $set, $record) {
                                             if($record){

                                                 $arTranslation = $record->translations()->where('locale', 'ar')->first();
           
                                                   if ($arTranslation?->value) {
                                                       $decodedValue = json_decode($arTranslation->value, true);
                                                       $description = $decodedValue['description'] ?? '<p></p>';
                                                       $set('ar.description', $description ?: '<p></p>');
                                                   } else {
                                                       $set('ar.description', '<p></p>');
                                                   }
                                             }
                                          })
                                        ->toolbarButtons([
                                            'blockquote', 'bold', 'bulletList', 'codeBlock',
                                            'h2', 'h3', 'italic', 'link', 'orderedList',
                                            'redo', 'strike', 'underline', 'undo',
                                        ]),
                                ]),
                            ])->columnSpanFull(),
                         ]) ->columnSpanFull(),
            ]);
    }
}
