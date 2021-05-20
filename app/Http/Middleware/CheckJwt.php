<?php

namespace App\Http\Middleware;

use App\Helpers\JwtAuth;
use Closure;

class CheckJwt
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

        $token = $request->header('Authorization');

        $jwt = new JwtAuth();
        
        $checked = $jwt->checkToken($token);

        if($checked){
            return $next($request);
        }else {
             $data = [
                'status' => 'failed',
                'code' => 401,
                'message' => 'Your not authorizated'
            ];
        }
        return response()->json($data, $data['code']);
    }
}
