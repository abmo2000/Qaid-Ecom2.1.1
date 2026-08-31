<?php

namespace App\Filament\Resources\TermsSettings\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class TermsSettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Terms & Conditions')
                    ->schema([
                        Tabs::make('Locale')
                            ->tabs([
                                Tab::make('English')
                                    ->schema([
                                        Textarea::make('en.terms')
                                            ->label('Terms (English)')
                                            ->rows(16)
                                            ->helperText('Enter the English terms and refund policy. HTML is supported.')
                                            ->required(),
                                    ]),
                                Tab::make('Arabic')
                                    ->schema([
                                        Textarea::make('ar.terms')
                                            ->label('Terms (Arabic)')
                                            ->rows(16)
                                            ->helperText('Enter the Arabic terms and refund policy. HTML is supported.')
                                            ->required(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
