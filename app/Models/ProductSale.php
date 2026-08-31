<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSale extends Model
{
    protected $guarded = ['id' , 'created_at' , 'updated_at'];


    public function product(){
        return $this->belongsTo(Product::class);
    }
}
