<?php

namespace App\Http\Middleware;

use App\Helpers\JwtAuth;
use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //COMPROBAR TOKEN
        $token = $request->header('Authorization');
        $jwt = new JwtAuth();
        if(!$jwt->checkToken($token)){
            return response()->json(['message' => 'Usuario no identificado'], 400);
        }
        return $next($request);
    }
}
