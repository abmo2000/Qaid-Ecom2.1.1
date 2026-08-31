<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Artisan;

class ArtisanController extends Controller
{
    public function __invoke()
    {
       Artisan::call('queue:work', [
            '--stop-when-empty' => true,
        ]);
        
    }
}
