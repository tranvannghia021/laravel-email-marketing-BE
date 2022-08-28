<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
                                 
        
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With')
            ->header('Access-Control-Expose-Headers', 'Authorization')
            ->header('Access-Control-Allow-Methods', '*');
    }
}
                        
