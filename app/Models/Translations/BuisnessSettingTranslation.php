<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class BuisnessSettingTranslation extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'value' => 'array',
    ];
}
