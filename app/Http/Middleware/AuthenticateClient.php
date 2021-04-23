<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Constants\ErrorCode as EC;
use App\Constants\ErrorMessage as EM;
use App\Helper;
use App\User;

class AuthenticateClient
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // dd($request->bearerToken());
        // dd($cekClientAuth);
        $bearer = $request->bearerToken();
        $cekClientAuth = User::where('api_token', $bearer)->first();

        if (!$cekClientAuth) {
            return Helper::createResponse(EC::UNAUTHORIZED, EM::UNAUTHORIZED);
        }

        return $next($request);
    }
}
