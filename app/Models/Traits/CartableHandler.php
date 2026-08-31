<?php

namespace App\Models\Traits;


trait CartableHandler{

       public function getCartPrice(): int
    {
        return (int) $this->price;
    }

    public function getCartName(): string
    {
      
        return $this->name;
    }

   

    public function getCartDescription(): ?string
    {
        return $this->description;
    }

}