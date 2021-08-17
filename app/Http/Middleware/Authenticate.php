<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Constants\ErrorCode as EC;
use App\Constants\ErrorMessage as EM;
use Firebase\JWT\JWT;
use App\Helper;

class Authenticate
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
        if ($this->auth->guard($guard)->guest()) {
            return Helper::createResponse(EC::UNAUTHORIZED, EM::UNAUTHORIZED);
        }
        $token = $request->bearerToken();
        $credentials = JWT::decode($token, 'iota-tracking', array('HS256'));
        $request->current_user = $credentials->sub;

        return $next($request);
    }
}
