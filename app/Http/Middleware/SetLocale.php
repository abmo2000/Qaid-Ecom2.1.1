<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

         if (Session::has('locale')) {
            $locale = Session::get('locale');
        }elseif ($request->has('lang')) {
            $locale = $request->get('lang');
            Session::put('locale', $locale);
        } else {
            $locale = config('app.locale');
        }
        
        App::setLocale($locale);
        return $next($request);
    }
}
