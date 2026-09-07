<?php
namespace App\Models;

use App\Models\Interfaces\Cartable;
use App\Models\Interfaces\ProductBaseInterface;
use App\Models\Traits\CartableHandler;
use App\Models\Traits\HasItems;
use App\Models\Traits\HasOrders;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Astrotomic\Translatable\Contracts\Translatable;
use Astrotomic\Translatable\Translatable as AstrotomicTranslatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model implements Translatable , Cartable
{
    use AstrotomicTranslatable , HasItems , CartableHandler , HasOrders, HasFactory;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $guarded = ['id' , 'created_at' , 'updated_at'];

    protected $attributes = [
        'stock' => 0,
        'in_stock' => false,
    ];

    protected $casts = [
        'images' => 'array',
        'stock' => 'integer',
        'in_stock' => 'boolean',
    ];

    public $translatedAttributes = ['name' , 'description' , 'meta_title' , 'meta_description', 'meta_keywords'];
     public $translationModel = \App\Models\Translations\ProductTranslation::class;
    public function routines():BelongsToMany{
        return $this->belongsToMany(Routine::class , 'products_routines');
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }


      public function packages():BelongsToMany{
        return $this->belongsToMany(Package::class , 'package_products');
    }

    public function sale():HasOne{
         return $this->hasOne(ProductSale::class);
    }

    public function isTrial():bool{
        return false;
    }

     public function getCartAlbum(): string|array
    {
        return $this->image;
    }

    public function getGalleryImagesAttribute(): array
    {
        $images = is_array($this->images) ? $this->images : [];

        return collect([$this->image, ...$images])
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function trial(){
        return $this->hasOne(ProductTrial::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ProductView::class);
    }

    public function getViewCountAttribute(): int
    {
        return $this->views()->count();
    }

    public function hasSale():bool{
        $this->loadMissing(['sale']);
         return ! empty($this->sale);
    }

    public function isOutOfStock(): bool
    {
        return ! $this->in_stock || ($this->stock !== null && $this->stock <= 0);
    }

     protected static function booted(): void
    {
        static::deleting(function (Product $product) {
            $images = is_array($product->images) ? $product->images : [];

            foreach (collect([$product->image, ...$images])->filter()->unique() as $image) {
                Storage::disk('local')->delete($image);
            }

        });

    }
}
