<?php

namespace App\Filament\Resources\Packages\Schemas;

use App\Models\Category;
use App\Models\Product;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Tabs\Tab;

class PackagesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make()
                ->schema([
                    Tabs::make('Translations')
                        ->tabs([
                            Tab::make('English')
                                ->schema([
                                    TextInput::make('en.name')
                                        ->label('Title (EN)')
                                        ->required()
                                        ->maxLength(255),

                                    RichEditor::make('en.description')
                                        ->label('Description (EN)')
                                        ->toolbarButtons([
                                            'blockquote', 'bold', 'bulletList', 'codeBlock',
                                            'h2', 'h3', 'italic', 'link', 'orderedList',
                                            'redo', 'strike', 'underline', 'undo',
                                        ]),
                                ]),

                            Tab::make('Arabic')
                                ->schema([
                                    TextInput::make('ar.name')
                                        ->label('Title (AR)')
                                        ->maxLength(255)
                                        ->required()
                                        ->extraAttributes(['dir' => 'rtl', 'style' => 'text-align:right;']),

                                    RichEditor::make('ar.description')
                                        ->label('Description (AR)')
                                        ->extraAttributes(['dir' => 'rtl'])
                                        ->toolbarButtons([
                                            'blockquote', 'bold', 'bulletList', 'codeBlock',
                                            'h2', 'h3', 'italic', 'link', 'orderedList',
                                            'redo', 'strike', 'underline', 'undo',
                                        ]),
                                ]),
                            ])->columnSpanFull(),
                         ]) ->columnSpanFull(),

                Section::make()
                ->schema([
                    TextInput::make('price')
                        ->label('Price')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->inputMode('decimal'),

                  
                        Toggle::make('is_active')->label('Is Active'),

                ])
                ->columns(2),

                Section::make('Products & Categories')
                ->schema([
                    Select::make('category_filter')
                        ->label('# Categories')
                        ->multiple()
                        ->searchable()
                        ->options(
                            Category::with('translations')
                                ->get()
                                ->mapWithKeys(fn ($category) => [$category->id => $category->title ?? ('Category #' . $category->id)])
                                ->toArray()
                        )
                        ->helperText('Select one or more categories to show only the related products for this package.')
                        ->afterStateUpdated(function (Set $set, $state) {
                            $set('products', []);
                        }),

                    Select::make('products')
                        ->label('Products')
                        ->required()
                        ->multiple()
                        ->options(function (Get $get) {
                            $categoryIds = $get('category_filter') ?? [];

                            return Product::query()
                                ->with(['translations', 'category.translations'])
                                ->when(! empty($categoryIds), fn ($query) => $query->whereIn('category_id', $categoryIds))
                                ->get()
                                ->mapWithKeys(function ($product) {
                                    $name = $product->translations->first()?->name ?? 'No title';
                                    $categoryTitle = $product->category?->title ?? 'Uncategorized';

                                    return [$product->id => $name . ' — ' . $categoryTitle];
                                });
                        })
                        ->searchable(false)
                        ->helperText('Only products from the selected categories are shown here.')
                        ->afterStateHydrated(function (Select $component, $state, $record) {
                            $selected = $record?->products()->pluck('products.id')->toArray() ?? [];
                            $component->state($selected);
                        })
                        ->saveRelationshipsUsing(function (Select $component, $state) {
                            $record = $component->getRecord();

                            if ($record && method_exists($record, 'products')) {
                                $record->products()->sync($state ?? []);
                            }
                        }),

                ])
                ->columns(1),
            ]);
    }
}
