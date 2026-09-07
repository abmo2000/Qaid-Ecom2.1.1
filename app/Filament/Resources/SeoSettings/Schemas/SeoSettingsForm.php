<?php

namespace App\Filament\Resources\SeoSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SeoSettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Page SEO')
                ->description('Each page has independent metadata. Leave a field empty to use the frontend fallback.')
                ->icon('heroicon-o-magnifying-glass')
                ->schema([
                    Tabs::make('Languages')
                        ->tabs([
                            self::languageTab('English', 'en', false),
                            self::languageTab('Arabic', 'ar', true),
                        ])
                        ->columnSpanFull(),
                ]),
        ]);
    }

    private static function languageTab(string $label, string $locale, bool $rtl): Tab
    {
        $direction = $rtl ? ['dir' => 'rtl', 'style' => 'text-align:right;'] : [];

        return Tab::make($label)->schema([
            TextInput::make($locale . '.meta_title')
                ->label('Meta Title (' . strtoupper($locale) . ')')
                ->maxLength(60)
                ->placeholder($rtl ? 'عنوان الصفحة لمحركات البحث' : 'Concise page title for search engines')
                ->helperText('Recommended: 50-60 characters. The field shows a live character counter.')
                ->extraAttributes($direction),

            Textarea::make($locale . '.meta_description')
                ->label('Meta Description (' . strtoupper($locale) . ')')
                ->rows(3)
                ->maxLength(160)
                ->placeholder($rtl ? 'وصف مختصر للصفحة في نتائج البحث' : 'Brief summary shown in search results')
                ->helperText('Recommended: 150-160 characters. The field shows a live character counter.')
                ->extraAttributes($rtl ? ['dir' => 'rtl'] : []),

            TextInput::make($locale . '.meta_keywords')
                ->label('Meta Keywords / Tags (' . strtoupper($locale) . ')')
                ->maxLength(255)
                ->placeholder($rtl ? 'كلمة، كلمة، كلمة' : 'keyword, keyword, keyword')
                ->helperText('Optional. Separate keywords or tags with commas.')
                ->extraAttributes($direction),
        ]);
    }
}