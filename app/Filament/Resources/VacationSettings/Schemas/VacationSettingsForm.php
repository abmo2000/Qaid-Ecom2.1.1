<?php

namespace App\Filament\Resources\VacationSettings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class VacationSettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Vacation Mode')
                ->schema([
                    Toggle::make('enabled')
                        ->label('Vacation Mode')
                        ->helperText('Block new order submissions while keeping browsing and cart building active.'),
                    Tabs::make('Vacation Messages')
                        ->tabs([
                            Tab::make('English')
                                ->schema([
                                    Textarea::make('en.message')
                                        ->label('Vacation Message (EN)')
                                        ->default("We're currently on a short break and can't process orders right now. We'll be back soon — thanks for your patience!")
                                        ->required()
                                        ->rows(4),
                                ]),
                            Tab::make('Arabic')
                                ->schema([
                                    Textarea::make('ar.message')
                                        ->label('Vacation Message (AR)')
                                        ->extraAttributes(['dir' => 'rtl'])
                                        ->default('نحن حاليًا في إجازة قصيرة ولا يمكننا معالجة الطلبات الآن. سنعود قريبًا — شكرًا لتفهمكم.')
                                        ->required()
                                        ->rows(4),
                                ]),
                        ])
                        ->columnSpanFull(),
                ])
                ->columns(1),
        ]);
    }
}
