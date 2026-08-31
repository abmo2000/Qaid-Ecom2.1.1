<?php

namespace App\Models\Translations;

use App\Models\Routine;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutineTranslation extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];


    public function routine():BelongsTo{
        return $this->belongsTo(Routine::class);
    }

     protected static function booted()
    {
      static::saved(function ($translation) {
             if ($translation->locale === 'en') {
               $routine = $translation->routine()->where('id' , $translation->routine_id)->first();
                $slug = Str::slug($translation->title . '-' .$routine->id);
                $routine->slug = $slug;
                $routine->saveQuietly();
             }

             
        }); 
    }
}
