<?php

namespace App\Http\Middleware;

use App\Http\Helpers\Constants;
use Closure;

use \App\Http\Helpers\ApiResponse;

class ApiKey
{
    use ApiResponse;

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

                $message = "Invalid API key";
                $errors = ["api_key" => "Invalid API key"];

                return $this->sendResponse(Constants::HTTP_UNAUTHORIZED, $message, null, $errors);
            }

            $message = "Missing API key header";
            $errors = ["api_key" => "x-api-key header is missing"];

            return $this->sendResponse(Constants::HTTP_UNAUTHORIZED, $message, null, $errors);

        }

        return $next($request);

    }
}
