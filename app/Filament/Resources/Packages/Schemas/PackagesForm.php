<?php

namespace App\Filament\Resources\Packages\Schemas;

use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class PackagesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->icon(Heroicon::OutlinedLanguage)
                    ->schema([
                        Tabs::make(__('packages.form.translations'))
                            ->tabs([
                                Tab::make(__('packages.form.english'))
                                    ->schema([
                                        TextInput::make('en.name')
                                            ->label(__('packages.form.title_english'))
                                            ->required()
                                            ->maxLength(255),
                                        RichEditor::make('en.description')
                                            ->label(__('packages.form.description_english'))
                                            ->toolbarButtons([
                                                'blockquote', 'bold', 'bulletList', 'codeBlock',
                                                'h2', 'h3', 'italic', 'link', 'orderedList',
                                                'redo', 'strike', 'underline', 'undo',
                                            ]),
                                    ]),
                                Tab::make(__('packages.form.arabic'))
                                    ->schema([
                                        TextInput::make('ar.name')
                                            ->label(__('packages.form.title_arabic'))
                                            ->required()
                                            ->maxLength(255)
                                            ->extraAttributes(['dir' => 'rtl', 'style' => 'text-align:right;']),
                                        RichEditor::make('ar.description')
                                            ->label(__('packages.form.description_arabic'))
                                            ->extraAttributes(['dir' => 'rtl'])
                                            ->toolbarButtons([
                                                'blockquote', 'bold', 'bulletList', 'codeBlock',
                                                'h2', 'h3', 'italic', 'link', 'orderedList',
                                                'redo', 'strike', 'underline', 'undo',
                                            ]),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make(__('packages.form.pricing_status'))
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->description(__('packages.form.pricing_status_help'))
                    ->schema([
                        TextInput::make('price')
                            ->label(__('packages.form.price'))
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->inputMode('decimal')
                            ->prefixIcon(Heroicon::OutlinedCurrencyDollar),
                        Toggle::make('is_active')
                            ->label(__('packages.form.is_active'))
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make(__('packages.form.package_contents'))
                    ->icon(Heroicon::OutlinedCube)
                    ->description(__('packages.form.package_contents_help'))
                    ->schema([
                        Select::make('category_filter')
                            ->label(__('packages.form.categories'))
                            ->multiple()
                            ->searchable()
                            ->live()
                            ->dehydrated(false)
                            ->options(fn (): array => Category::query()
                                ->with('translations')
                                ->orderBy('id')
                                ->get()
                                ->mapWithKeys(fn (Category $category): array => [
                                    $category->id => $category->title ?? __('packages.form.unnamed_category', ['id' => $category->id]),
                                ])
                                ->all())
                            ->helperText(__('packages.form.categories_help')),

                        Select::make('products')
                            ->label(__('packages.form.products'))
                            ->multiple()
                            ->required()
                            ->relationship(name: 'products', titleAttribute: 'id')
                            ->searchable()
                            ->searchDebounce(350)
                            ->optionsLimit(50)
                            ->searchPrompt(__('packages.form.products_search_prompt'))
                            ->searchingMessage(__('packages.form.products_searching'))
                            ->noSearchResultsMessage(__('packages.form.products_no_results'))
                            ->getSearchResultsUsing(function (?string $search, Get $get): array {
                                $search = trim((string) $search);

                                if ($search === '') {
                                    return [];
                                }

                                $categoryIds = $get('category_filter') ?? [];

                                // Include unpublished products too, matching the previous package selector behavior.
                                return Product::query()
                                    ->whereHas('translations', fn (Builder $query) => $query
                                        ->whereIn('locale', ['en', 'ar'])
                                        ->where('name', 'like', $search.'%'))
                                    ->when($categoryIds !== [], fn (Builder $query) => $query->whereIn('category_id', $categoryIds))
                                    ->with([
                                        'translations' => fn ($query) => $query->whereIn('locale', ['en', 'ar']),
                                        'category.translations',
                                    ])
                                    ->orderBy('id')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn (Product $product): array => [
                                        $product->id => self::productLabel($product),
                                    ])
                                    ->all();
                            })
                            ->getOptionLabelsUsing(function (array $values): array {
                                if ($values === []) {
                                    return [];
                                }

                                return Product::query()
                                    ->whereKey($values)
                                    ->with([
                                        'translations' => fn ($query) => $query->whereIn('locale', ['en', 'ar']),
                                        'category.translations',
                                    ])
                                    ->get()
                                    ->mapWithKeys(fn (Product $product): array => [
                                        $product->id => self::productLabel($product),
                                    ])
                                    ->all();
                            })
                            ->helperText(__('packages.form.products_help')),
                    ])
                    ->columns(1),
            ]);
    }

    private static function productLabel(Product $product): string
    {
        $locale = app()->getLocale();
        $translation = $product->translations->firstWhere('locale', $locale)
            ?? $product->translations->firstWhere('locale', 'en')
            ?? $product->translations->firstWhere('locale', 'ar');
        $name = $translation?->name ?? __('packages.form.unnamed_product', ['id' => $product->id]);
        $category = $product->category?->title ?? __('packages.form.uncategorized');

        return $name.' — '.$category.' · '.number_format((float) $product->price, 0);
    }
}
