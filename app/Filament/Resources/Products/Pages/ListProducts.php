<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Routine;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_import_template')
                ->label('Download import template')
                ->icon('heroicon-o-arrow-down-circle')
                ->url(fn (): string => asset('import-templates/product-import-template.csv'))
                ->openUrlInNewTab()
                ->visible(fn (): bool => ProductResource::canManageProducts()),

            Action::make('import_products')
                ->label('Import Products')
                ->icon('heroicon-o-arrow-up')
                ->form([
                    FileUpload::make('file')
                        ->label('CSV or XLSX file')
                        ->required()
                        ->disk('local')
                        ->directory('imports')
                        ->acceptedFileTypes([
                            '.csv',
                            'text/csv',
                            'text/plain',
                            'application/csv',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            '.xlsx',
                            '.xls',
                            'application/octet-stream',
                        ]),
                ])
                ->action(function (array $data): void {
                    Log::debug('ImportProducts action data', $data);
                    Notification::make()
                        ->title('Import debug')
                        ->info()
                        ->body(isset($data['file']) ? 'file present: ' . $data['file'] : 'no file uploaded')
                        ->send();
                    $path = Storage::disk('local')->path($data['file']);
                    [$importedCount, $errors] = $this->importProductsFromFile($path);

                    $message = "Imported {$importedCount} products.";
                    if (count($errors) > 0) {
                        Notification::make()
                            ->title('Import completed with warnings')
                            ->warning()
                            ->body(
                                $message . ' ' . implode(' ', array_slice($errors, 0, 5)) . (count($errors) > 5 ? ' ...' : '')
                            )
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Products imported successfully')
                            ->success()
                            ->body($message)
                            ->send();
                    }
                })
                ->visible(fn (): bool => ProductResource::canManageProducts()),

            CreateAction::make()
                ->visible(fn (): bool => ProductResource::canManageProducts()),
        ];
    }

    private function importProductsFromFile(string $path): array
    {
        $rows = $this->loadRowsFromFile($path);

        if (empty($rows)) {
            return [0, ['The import file is empty or could not be read.']];
        }

        $headers = array_map(static fn ($header): string => strtolower(trim((string) $header)), array_shift($rows));
        $importedCount = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            if ($this->isBlankRow($row)) {
                continue;
            }

            $rowData = $this->buildRowData($headers, $row);
            $rowData = $this->normalizeImportRow($rowData);

            $result = $this->importProductRow($rowData, $index + 2);
            if ($result['success']) {
                $importedCount++;
            }

            if ($result['warning']) {
                $errors[] = $result['warning'];
            }
        }

        return [$importedCount, $errors];
    }

    private function loadRowsFromFile(string $path): array
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === 'csv') {
            return $this->loadRowsFromCsv($path);
        }

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            return $this->loadRowsFromXlsx($path);
        }

        throw new \RuntimeException('Unsupported file type. Please upload a CSV or XLSX file.');
    }

    private function loadRowsFromCsv(string $path): array
    {
        $rows = [];

        if (($handle = fopen($path, 'r')) === false) {
            return $rows;
        }

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function loadRowsFromXlsx(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();

        return $worksheet->toArray(null, true, true, false);
    }

    private function isBlankRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function buildRowData(array $headers, array $row): array
    {
        $result = [];

        foreach ($headers as $columnIndex => $header) {
            $result[$header] = isset($row[$columnIndex]) ? trim((string) $row[$columnIndex]) : null;
        }

        return $result;
    }

    private function normalizeImportRow(array $row): array
    {
        $map = [
            'english_name' => 'en_name',
            'name_en' => 'en_name',
            'arabic_name' => 'ar_name',
            'name_ar' => 'ar_name',
            'english_description' => 'en_description',
            'description_en' => 'en_description',
            'arabic_description' => 'ar_description',
            'description_ar' => 'ar_description',
            'title_en' => 'en_name',
            'title_ar' => 'ar_name',
            'category_name' => 'category_name',
            'categoryid' => 'category_id',
            'category_id' => 'category_id',
            'in_stock' => 'in_stock',
            'stock' => 'stock',
            'stok' => 'stock',
            'featured' => 'featured',
            'has_sale' => 'has_sale',
            'sale_price' => 'sale_price',
        ];

        $normalized = [];

        foreach ($row as $key => $value) {
            $key = strtolower(str_replace([' ', '-'], '_', trim((string) $key)));

            if ($key === 'name') {
                $normalized['en_name'] = $value;
                continue;
            }

            if ($key === 'description') {
                $normalized['en_description'] = $value;
                continue;
            }

            $normalized[$map[$key] ?? $key] = $value;
        }

        return $normalized;
    }

    private function importProductRow(array $row, int $rowNumber): array
    {
        $warnings = [];

        $enName = trim((string) ($row['en_name'] ?? ''));
        $arName = trim((string) ($row['ar_name'] ?? ''));
        $enDescription = trim((string) ($row['en_description'] ?? ''));
        $arDescription = trim((string) ($row['ar_description'] ?? ''));
        $price = $row['price'] ?? null;
        $capacity = $row['capacity'] ?? null;
        $stock = $row['stock'] ?? null;
        $brandName = trim((string) ($row['brand'] ?? ''));
        $categoryName = trim((string) ($row['category_name'] ?? ''));
        $categoryId = $row['category_id'] ?? null;

        if ($enName === '') {
            $warnings[] = 'english name';
            $enName = "Pending product import #{$rowNumber}";
        }

        if ($arName === '') {
            $warnings[] = 'arabic name';
            $arName = "منتج معلق رقم {$rowNumber}";
        }

        if ($enDescription === '') {
            $warnings[] = 'english description';
            $enDescription = 'Pending import description.';
        }

        if ($arDescription === '') {
            $warnings[] = 'arabic description';
            $arDescription = 'وصف مستورد معلق.';
        }

        if ($price === null || $price === '' || ! is_numeric($price)) {
            $warnings[] = 'price';
            $price = 0;
        }

        if ($capacity === null || $capacity === '' || ! is_numeric($capacity)) {
            $warnings[] = 'capacity';
            $capacity = 0;
        }

        if ($stock !== null && $stock !== '') {
            if (! is_numeric($stock)) {
                $warnings[] = 'stock';
                $stock = 0;
            } else {
                $stock = (int) $stock;
            }
        } else {
            $stock = 0;
        }

        if ($brandName === '') {
            $warnings[] = 'brand';
            $brandName = 'Unknown Brand';
        }

        $category = $this->resolveCategory($categoryId, $categoryName, true);
        if ($category === null) {
            return [
                'success' => false,
                'warning' => "Row {$rowNumber}: category could not be resolved or created.",
            ];
        }

        if ($categoryName === '' && empty($categoryId)) {
            $warnings[] = 'category';
        }

        Brand::firstOrCreate([
            'name' => $brandName,
        ], [
            'image' => null,
        ]);

        try {
            $stock = $stock ?? 0;
            $inStock = $this->normalizeBoolean($row['in_stock'] ?? 'yes');
            if ($stock <= 0) {
                $inStock = false;
            }

            $product = Product::create([
                'image' => '',
                'price' => (float) $price,
                'capacity' => (int) $capacity,
                'stock' => $stock,
                'brand' => $brandName,
                'category_id' => $category->id,
                'in_stock' => $inStock,
                'featured' => $this->normalizeBoolean($row['featured'] ?? 'no'),
                'is_published' => false,
                'import_error' => empty($warnings) ? null : 'Pending import: ' . implode(', ', $warnings),
            ]);

            $product->translateOrNew('en')->fill([
                'name' => $enName,
                'description' => $enDescription,
            ])->save();

            $product->translateOrNew('ar')->fill([
                'name' => $arName,
                'description' => $arDescription,
            ])->save();

            if ($this->normalizeBoolean($row['has_sale'] ?? 'no') && ! empty($row['sale_price'])) {
                $product->sale()->create([
                    'sale_price' => (float) $row['sale_price'],
                ]);
            }


        } catch (\Throwable $exception) {
            return [
                'success' => false,
                'warning' => "Row {$rowNumber}: failed to import ({$exception->getMessage()}).",
            ];
        }

        return [
            'success' => true,
            'warning' => empty($warnings) ? null : "Row {$rowNumber}: imported as pending due to missing or invalid " . implode(', ', $warnings) . ".",
        ];
    }

    private function resolveCategory($categoryId, ?string $categoryName, bool $allowFallback = false): ?Category
    {
        if (! empty($categoryId) && is_numeric($categoryId)) {
            $category = Category::find((int) $categoryId);
            if ($category !== null) {
                return $category;
            }
        }

        if (! empty($categoryName)) {
            $categoryName = trim((string) $categoryName);

            $category = Category::whereHas('translations', function ($query) use ($categoryName) {
                $query->where('title', $categoryName);
            })->first();

            if ($category !== null) {
                return $category;
            }

            return Category::create([
                'en' => ['title' => $categoryName],
                'ar' => ['title' => $categoryName],
            ]);
        }

        if ($allowFallback) {
            return $this->getFallbackImportCategory();
        }

        return null;
    }

    private function getFallbackImportCategory(): Category
    {
        $title = 'Uncategorized';

        $category = Category::whereHas('translations', function ($query) use ($title) {
            $query->where('title', $title);
        })->first();

        if ($category !== null) {
            return $category;
        }

        return Category::create([
            'en' => ['title' => $title],
            'ar' => ['title' => 'غير مصنف'],
        ]);
    }

    private function resolveRoutineIds(string $value): array
    {
        $parts = array_filter(array_map(static fn ($item): string => trim($item), preg_split('/[;,]+/', $value)));
        if (empty($parts)) {
            return [];
        }

        $ids = array_filter($parts, static fn ($item): bool => is_numeric($item));
        $routineQuery = Routine::query();

        if (! empty($ids)) {
            $routineQuery->orWhereIn('id', array_map('intval', $ids));
        }

        $routineQuery->orWhereHas('translations', function ($query) use ($parts) {
            $query->whereIn('title', $parts);
        });

        return $routineQuery->pluck('id')->unique()->values()->all();
    }

    private function normalizeBoolean(string $value): bool
    {
        $value = strtolower(trim($value));

        return in_array($value, ['1', 'true', 'yes', 'y', 'on'], true);
    }
}
