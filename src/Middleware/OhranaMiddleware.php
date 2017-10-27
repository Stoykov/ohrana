<?php
namespace stoykov\Ohrana\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class OhranaMiddleware {
    protected $router;

    /**
     * The currently authenticated user
     * @var Illuminate\Contracts\Auth\Authenticatable
     */
    protected $user;

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
    public function handle($request, Closure $next, $guard=null)
    {
        $action = $request->route()[1]['uses'];

        if (!$this->auth->guard($guard)->getUser()->hasPermisson($action))
            return response('Unauthorized.', 401);

        return $next($request);
    }
}