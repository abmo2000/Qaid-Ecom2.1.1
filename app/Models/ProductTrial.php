<?php

namespace App\Models;


use App\Models\Interfaces\Cartable;
use App\Models\Traits\CartableHandler;
use App\Models\Traits\HasItems;
use App\Models\Traits\HasOrders;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTrial extends Model implements Cartable
{
    use HasItems , CartableHandler , HasOrders;
    protected $guarded = ['id' , 'created_at' , 'updated_at'];

  protected $appends = ['name', 'slug', 'description', 'category', 'routines'];
    public function product():BelongsTo{
        return $this->belongsTo(Product::class);
    }

    public function hasSale(){
        return false;
    }


    public function name():Attribute{
        return new Attribute(
            get:fn()=>$this->product?->name
        );
    }


      public function slug():Attribute{
        return new Attribute(
            get:fn()=>$this->product?->slug
        );
    }


     public function category():Attribute{
        return new Attribute(
            get:fn()=>$this->product?->category
        );
    }

    public function isTrial():bool{
        return true;
    }

     public function getCartAlbum(): string|array
    {
        return $this->image;
    }


    public function description():Attribute{
        return new Attribute(
            get:fn()=>$this->product?->description
        );
    }


    public function routines():Attribute{
         return new Attribute(
            get:fn()=>$this->product?->routines
        );
    }
    
}
