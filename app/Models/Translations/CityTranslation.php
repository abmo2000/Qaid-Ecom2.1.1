<?php

namespace App\Models\Translations;

use App\Models\City;
use Illuminate\Database\Eloquent\Model;

class CityTranslation extends Model
{
      public $timestamps = false;

    protected $guarded = ['id'];

     public function city(){
      return $this->belongsTo(City::class);
      
    }
}
