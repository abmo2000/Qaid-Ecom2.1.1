<?php

namespace App\Filament\Resources\Products\Pages;

use App\Models\Brand;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Products\ProductResource;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;
    protected array $translations = [];

    protected array $saleData = [];

    protected ?string $brandImage = null;

    protected ?string $brandName = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }


              protected function mutateFormDataBeforeFill(array $data): array
    {
       
        // Load translations into form
        $data['en'] = $this->record->translate('en')?->toArray() ?? [];
        $data['ar'] = $this->record->translate('ar')?->toArray() ?? [];

        if (! empty($data['brand'])) {
            $data['brand_image'] = Brand::query()
                ->where('name', trim((string) $data['brand']))
                ->value('image');
        }
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
      
        // Extract translations
        $translations = [];
        foreach (['en', 'ar'] as $locale) {
            if (isset($data[$locale])) {
                $translations[$locale] = $data[$locale];
                unset($data[$locale]);
            }
        }
        
        // Store translations separately
        $this->translations = $translations;

         $data['_sale'] = [
            'enabled' => $data['has_sale'] ?? false,
            'price' => $data['sale_price'] ?? null,
        ];
        
        unset(
            $data['has_sale'],
            $data['sale_price'],
        );
        $this->saleData = $data['_sale'];
        
        $this->brandImage = $data['brand_image'] ?? null;
                $this->brandName = ! empty($data['brand']) ? trim((string) $data['brand']) : null;
                unset($data['brand_image']);

        return $data;
    }
    
    protected function afterSave(): void
    {
        // Save translations
        if (isset($this->translations)) {
            foreach ($this->translations as $locale => $translation) {
                $this->record->translateOrNew($locale)->fill($translation)->save();
            }
        }

        $sale = $this->saleData;

        if ($sale['enabled']) {
            $this->record->sale()->updateOrCreate(
                [],
                [
                    'sale_price' => $sale['price'],
                ]
            );
        } else {
            $this->record->sale()?->delete();
        }

        $this->syncSharedBrandImage();
    }

    protected function syncSharedBrandImage(): void
    {
        if (empty($this->brandName)) {
            return;
        }

        $brand = Brand::query()->firstOrCreate(
            ['name' => $this->brandName],
            ['image' => null],
        );

        if (empty($this->brandImage)) {
            return;
        }

        $oldImage = $brand->image;

        $brand->update([
            'image' => $this->brandImage,
        ]);

        if (! empty($oldImage) && $oldImage !== $this->brandImage) {
            Storage::disk('public')->delete($oldImage);
        }
    }
}
