<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesaleRequest extends Model
{
    protected $fillable = [
        'business_name',
        'phone',
        'message',
        'status',
        'ip_address',
        'user_agent',
        'os',
        'locale',
    ];

    protected $attributes = [
        'status' => 'new',
    ];
}