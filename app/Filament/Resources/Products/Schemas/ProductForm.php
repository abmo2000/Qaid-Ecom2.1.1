<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Routine;
use App\Models\Category;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // Translations & Image side-by-side
            Section::make()
                ->schema([
                    Tabs::make('Translations')
                        ->tabs([
                            Tab::make('English')
                                ->schema([
                                    TextInput::make('en.name')
                                        ->label('Name (EN)')
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

                                    Section::make('SEO')
                                        ->description('Search engine optimization meta tags for English')
                                        ->collapsible()
                                        ->schema([
                                            TextInput::make('en.meta_title')
                                                ->label('Meta Title (EN)')
                                                ->maxLength(60)
                                                ->placeholder('Concise page title for search engines (max 60 chars)')
                                                ->helperText('Leave blank to use the product name'),

                                            Textarea::make('en.meta_description')
                                                ->label('Meta Description (EN)')
                                                ->rows(3)
                                                ->maxLength(160)
                                                ->placeholder('Brief summary shown in search results (max 160 chars)')
                                                ->helperText('Leave blank to use the product description'),
                                        ]),
                                ]),

                            Tab::make('Arabic')
                                ->schema([
                                    TextInput::make('ar.name')
                                        ->label('Name (AR)')
                                        ->required()
                                        ->maxLength(255)
                                        ->extraAttributes(['dir' => 'rtl', 'style' => 'text-align:right;']),

                                    RichEditor::make('ar.description')
                                        ->label('Description (AR)')
                                        ->required()
                                        ->extraAttributes(['dir' => 'rtl'])
                                        ->toolbarButtons([
                                            'blockquote', 'bold', 'bulletList', 'codeBlock',
                                            'h2', 'h3', 'italic', 'link', 'orderedList',
                                            'redo', 'strike', 'underline', 'undo',
                                        ]),

                                    Section::make('SEO')
                                        ->description('Search engine optimization meta tags for Arabic')
                                        ->collapsible()
                                        ->schema([
                                            TextInput::make('ar.meta_title')
                                                ->label('Meta Title (AR)')
                                                ->maxLength(60)
                                                ->placeholder('عنوان الصفحة لمحركات البحث (60 حرف كحد أقصى)')
                                                ->extraAttributes(['dir' => 'rtl', 'style' => 'text-align:right;'])
                                                ->helperText('اتركه فارغاً لاستخدام اسم المنتج'),

                                            Textarea::make('ar.meta_description')
                                                ->label('Meta Description (AR)')
                                                ->rows(3)
                                                ->maxLength(160)
                                                ->placeholder('وصف مختصر يظهر في نتائج البحث (160 حرف كحد أقصى)')
                                                ->extraAttributes(['dir' => 'rtl'])
                                                ->helperText('اتركه فارغاً لاستخدام وصف المنتج'),
                                        ]),
                                ]),
                            ])->columnSpanFull(),
                         ]) ->columnSpanFull(),

                     FileUpload::make('image')
                        ->label('Image')
                        ->required()
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('products')
                        ->visibility('public')
                        ->columnSpanFull(),

                    FileUpload::make('images')
                        ->label('Gallery Images')
                        ->image()
                        ->imageEditor()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('products')
                        ->visibility('public')
                        ->columnSpanFull(),
                        Section::make()
                            ->schema([
                                TextInput::make('price')
                                    ->label('Price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->inputMode('decimal'),

                                    TextInput::make('brand')
                                    ->label('Brand')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('stock')
                                    ->label('Stock')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('Set the current stock level. Use 0 to mark items as out of stock.'),

                                    FileUpload::make('brand_image')
                                        ->label('Brand Image (shared per brand)')
                                        ->image()
                                        ->imageEditor()
                                        ->directory('brands')
                                        ->visibility('public'),

                                Select::make('category_id')
                                    ->label('Category')
                                    ->required()
                                    ->options(
                                        Category::with('translations')
                                            ->get()
                                            ->pluck('title', 'id')
                                    )
                                    ->searchable(),
                            ])
                            ->columns(2),

                        // Featured + In Stock
                        Section::make()
                            ->schema([
                                Toggle::make('featured')->label('Featured'),
                                Toggle::make('in_stock')->label('In Stock'),
                            ])
                            ->columns(2),

                        
                       //sale  
                        Section::make('Sale')
                        ->schema([
                            Toggle::make('has_sale')
                                ->label('Has Sale')
                                ->reactive()
                                ->afterStateHydrated(function ($state, callable $set, $record) {
                                    $set('has_sale', $record?->sale()->exists());
                                }),

                                Group::make([
                                    TextInput::make('sale_price')
                                        ->label('Sale Price')
                                        ->numeric()
                                        ->required(fn (Get $get) => $get('has_sale'))
                                        ->afterStateHydrated(function ($state, callable $set, $record) {
                                                $set('sale_price', $record?->sale?->sale_price);
                                        })
                                        ->minValue(1),
                                ])
                                ->columns(1)
                                ->visible(fn (Get $get) => $get('has_sale')),

                            ]) ->columns(2), 
        ]);
    }
}