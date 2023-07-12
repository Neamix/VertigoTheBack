<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizedMiddleware
{
    /**
     * Load auth on api guard if it exist check that if he is suspended or not in case of suspended block request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        dd('here');
        // Check if current authed user not suspended 
        // if ( Auth::guard('api')->check() ) {
        //     if ( ! Auth::guard('api')->user()->active ) {
        //         return response()->json([
        //             'message' => 'Forbiden'
        //         ], 419);
        //     }
        // }

        return $next($request);
    }
}
