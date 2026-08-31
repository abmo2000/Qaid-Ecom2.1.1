<?php

namespace App\Factories;

use App\Managers\SessionStorage;


class CartStorageFactory{

    public static function getInstance(): \App\Interfaces\CartStorageInterface
    {
        // if (Auth::check()) {
        //     return new DatabaseStorage();
        // }
        
        return new SessionStorage();
    }
}