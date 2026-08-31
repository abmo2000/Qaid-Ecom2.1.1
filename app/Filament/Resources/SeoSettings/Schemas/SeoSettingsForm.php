<?php

namespace App\Filament\Resources\SeoSettings\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;

class SeoSettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Home Page SEO Settings')
                    ->description('Configure search engine optimization (SEO) meta tags for your home page to improve visibility and click-through rates in search results.')
                    ->icon('heroicon-o-magnifying-glass')
                    ->schema([
                        Tabs::make('Languages')
                            ->tabs([
                                Tab::make('English')
                                    ->schema([
                                        TextInput::make('en.meta_title')
                                            ->label('Meta Title (EN)')
                                            ->maxLength(60)
                                            ->required()
                                            ->placeholder('Your shop title - Max 60 characters')
                                            ->afterStateHydrated(function ($state, callable $set, $record) {
                                                if ($record) {
                                                    $enTranslation = $record->translations()->where('locale', 'en')->first();
                                                    if ($enTranslation?->value) {
                                                        $decodedValue = json_decode($enTranslation->value, true);
                                                        $set('en.meta_title', $decodedValue['meta_title'] ?? '');
                                                    }
                                                }
                                            })
                                            ->helperText('This appears in browser tabs and search results. Keep it under 60 characters.'),

                                        Textarea::make('en.meta_description')
                                            ->label('Meta Description (EN)')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->required()
                                            ->placeholder('Brief summary of your shop - Max 160 characters')
                                            ->afterStateHydrated(function ($state, callable $set, $record) {
                                                if ($record) {
                                                    $enTranslation = $record->translations()->where('locale', 'en')->first();
                                                    if ($enTranslation?->value) {
                                                        $decodedValue = json_decode($enTranslation->value, true);
                                                        $set('en.meta_description', $decodedValue['meta_description'] ?? '');
                                                    }
                                                }
                                            })
                                            ->helperText('This summary appears below the title in search results. Keep it under 160 characters.'),

                                        TextInput::make('en.meta_keywords')
                                            ->label('Meta Keywords (EN)')
                                            ->maxLength(255)
                                            ->placeholder('keyword1, keyword2, keyword3')
                                            ->afterStateHydrated(function ($state, callable $set, $record) {
                                                if ($record) {
                                                    $enTranslation = $record->translations()->where('locale', 'en')->first();
                                                    if ($enTranslation?->value) {
                                                        $decodedValue = json_decode($enTranslation->value, true);
                                                        $set('en.meta_keywords', $decodedValue['meta_keywords'] ?? '');
                                                    }
                                                }
                                            })
                                            ->helperText('Comma-separated keywords relevant to your store (optional)'),
                                    ]),

                                Tab::make('Arabic')
                                    ->schema([
                                        TextInput::make('ar.meta_title')
                                            ->label('Meta Title (AR)')
                                            ->maxLength(60)
                                            ->required()
                                            ->placeholder('عنوان متجرك - 60 حرف كحد أقصى')
                                            ->extraAttributes(['dir' => 'rtl', 'style' => 'text-align:right;'])
                                            ->afterStateHydrated(function ($state, callable $set, $record) {
                                                if ($record) {
                                                    $arTranslation = $record->translations()->where('locale', 'ar')->first();
                                                    if ($arTranslation?->value) {
                                                        $decodedValue = json_decode($arTranslation->value, true);
                                                        $set('ar.meta_title', $decodedValue['meta_title'] ?? '');
                                                    }
                                                }
                                            })
                                            ->helperText('يظهر في تبويبات المتصفح ونتائج البحث. احفظه أقل من 60 حرف.'),

                                        Textarea::make('ar.meta_description')
                                            ->label('Meta Description (AR)')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->required()
                                            ->placeholder('وصف موجز لمتجرك - 160 حرف كحد أقصى')
                                            ->extraAttributes(['dir' => 'rtl'])
                                            ->afterStateHydrated(function ($state, callable $set, $record) {
                                                if ($record) {
                                                    $arTranslation = $record->translations()->where('locale', 'ar')->first();
                                                    if ($arTranslation?->value) {
                                                        $decodedValue = json_decode($arTranslation->value, true);
                                                        $set('ar.meta_description', $decodedValue['meta_description'] ?? '');
                                                    }
                                                }
                                            })
                                            ->helperText('يظهر هذا الوصف تحت العنوان في نتائج البحث. احفظه أقل من 160 حرف.'),

                                        TextInput::make('ar.meta_keywords')
                                            ->label('Meta Keywords (AR)')
                                            ->maxLength(255)
                                            ->placeholder('كلمة1، كلمة2، كلمة3')
                                            ->extraAttributes(['dir' => 'rtl', 'style' => 'text-align:right;'])
                                            ->afterStateHydrated(function ($state, callable $set, $record) {
                                                if ($record) {
                                                    $arTranslation = $record->translations()->where('locale', 'ar')->first();
                                                    if ($arTranslation?->value) {
                                                        $decodedValue = json_decode($arTranslation->value, true);
                                                        $set('ar.meta_keywords', $decodedValue['meta_keywords'] ?? '');
                                                    }
                                                }
                                            })
                                            ->helperText('كلمات مفتاحية مرغوبة لمتجرك (اختياري)'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
