<?php

namespace App\Models\Translations;

use App\Models\Package;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class PackageTranslation extends Model
{
     public $timestamps = false;

    protected $guarded = ['id'];


     public function package(){
      return $this->belongsTo(Package::class);
      
    }


      protected static function booted()
    {
      static::saved(function ($translation) {
             if ($translation->locale === 'en') {
               $package = $translation->package()->where('id' , $translation->package_id)->first();
               $name = $translation->name ?? $translation->title ?? 'Package';
                $slug = Str::slug($name . '-' .$package->id);
                $package->slug = $slug;
                $package->saveQuietly();
             }
        }); 
    }
}
