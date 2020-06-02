<?php

namespace App\Http\Middleware;

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
        $jwt= new \JwtAuth();
        $token=$request->header("authorization");
        $token=str_replace('"',"", $token);
        $checktoken=$jwt->checktoken($token);
        if ($checktoken)
        {
            return $next($request);
        }
        else
        {
            $response=array('code'=>400
                         ,'message'=>'Usuario no identificado  Por favor inicie sesion para continuar'
                         ,'status'=>'Error');
            return response()->json($response,$response['code']);
        }
    }
}
