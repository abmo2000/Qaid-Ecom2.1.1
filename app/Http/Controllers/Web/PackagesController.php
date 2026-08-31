<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    public function show(string $slug){
       $package = Package::query()
        ->with(['products'])
        ->where('slug' , $slug)
        ->firstOrFail();


        return view('web.pages.shop.packages.show')->with(['package' => $package]);
    }
}
