<?php

namespace App\Models;

use App\Models\Traits\HasProducts;
use Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Astrotomic\Translatable\Contracts\Translatable;
use Astrotomic\Translatable\Translatable as AstrotomicTranslatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Routine extends Model implements Translatable
{
    use AstrotomicTranslatable , HasProducts; 

    protected $guarded = ['id' , 'created_at' , 'updated_at'];
    public $translatedAttributes = ['title' , 'description'];
    

     public $translationModel = \App\Models\Translations\RoutineTranslation::class;

  

     public function products():BelongsToMany
     {
        return $this->belongsToMany(Product::class , 'products_routines');
     }

  protected static function booted(): void
    {
        static::deleting(function (Routine $routine) {
            // Delete the image file when the routine is deleted
            if ($routine->image) {
                Storage::disk('local')->delete($routine->image);
            }
        });
    }



}
