<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable;
use Astrotomic\Translatable\Translatable as AstrotomicTranslatable;

class BuisnessSetting extends Model implements Translatable
{
    use AstrotomicTranslatable;
    protected $guarded = [ 'id' , 'created_at' , 'updated_at'];

    public $translatedAttributes = ['value', 'meta_title', 'meta_description', 'meta_keywords'];

     public $translationModel = \App\Models\Translations\BuisnessSettingTranslation::class;


}
