<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'is_reviewed',
    ];

    protected $casts = [
        'is_reviewed' => 'boolean',
    ];
}
