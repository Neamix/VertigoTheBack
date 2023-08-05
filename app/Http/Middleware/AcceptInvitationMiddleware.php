<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\JoinRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AcceptInvitationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $requestInstance = JoinRequest::where('email',$request->email)->first();

        if ( ! $requestInstance ) {
            abort(404);
        }

        $verifiedToken = Hash::check($request->token,$requestInstance->token);
        $companyExist  = Company::where(['id' => $requestInstance->company_id])->first();

        if ( ! $companyExist || ! $verifiedToken) {
            abort(404);
        }

        return $next($request);
    }
}
