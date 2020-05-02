<?php

namespace App\Http\Middleware;

use Closure;

class ApiKey
{
    /**
     * @author Hamza al Darawsheh <ihamzehald@gmail.com>
     * If ACTIVATE_API_KEY true allow requests from clients who only send x-api-key header
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(env('ACTIVATE_API_KEY', false)){

            $apiKey = $request->header('x-api-key');

            if($apiKey){
                if($apiKey === env('API_KEY', false)){
                    return $next($request);
                }
                return response()->json(['error' => 'x-api-key invalid'], 401);
            }

            return response()->json(['error' => 'x-api-key missing'], 401);

        }

        return $next($request);

    }
}
