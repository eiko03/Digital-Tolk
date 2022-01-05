<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!isset($request->__authenticatedUser)){
            return response (['message'=>'Unauthorized'],401);
        }
        return $next($request);
    }
}