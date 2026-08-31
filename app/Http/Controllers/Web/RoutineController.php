<?php

namespace App\Http\Controllers\Web;

use App\Models\Routine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoutineController extends Controller
{
    public function index(){

       $routines = Routine::query()
       ->with(['translations' => fn($q) => $q->where('locale' , app()->getLocale())])
       ->get();
        
        return view('web.pages.routine.index')->with(['routines' => $routines]);
    }


    public function show(string $routine_slug){
        $routine = Routine::query()
        ->with(['translations' => fn($q) => $q->where('locale' , app()->getLocale()) , 'products'])
        ->where('slug' , $routine_slug)
        ->firstOrFail();
           
        return view('web.pages.routine.show')->with(['routine' => $routine]);

    }
}
