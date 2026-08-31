<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Token') ?? $request->query('token');
        
        if ($token !== substr(hash('sha256',config('app.artisan_token')) , 0 , 16) ) {
            abort(401, 'Invalid API token.');
        }
        
        return $next($request);
    }
}
