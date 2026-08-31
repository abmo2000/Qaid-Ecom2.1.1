<?php

namespace App\Models;

use App\Models\Traits\HasProducts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Astrotomic\Translatable\Contracts\Translatable;
use Astrotomic\Translatable\Translatable as AstrotomicTranslatable;

class Category extends Model implements Translatable
{
    use AstrotomicTranslatable , HasProducts;     
    
    protected $guarded = ['id' , 'created_at' , 'updated_at'];


    public $translatedAttributes = ['title'];

     public $translationModel = \App\Models\Translations\CategoryTranslation::class;

      protected static function booted(): void
    {
        static::deleting(function (Category $category) {
            // Delete the image file when the routine is deleted
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
        });
    }
}
