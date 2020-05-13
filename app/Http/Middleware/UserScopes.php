<?php

namespace App\Http\Middleware;

use Closure;

class UserScopes{
    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next){

        if($request->user()->tokenCan('user')){
            return $next($request);
        }
        return response()->json([
            'message' => "Unauthenticated",
        ], 401);
    }
}
