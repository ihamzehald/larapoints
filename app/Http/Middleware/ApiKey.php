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

        if (env('ACTIVATE_API_KEY', false)) {
            $apiKey = $request->header('x-api-key');

            if ($apiKey) {
                if ($apiKey === env('API_KEY', false)) {
                    return $next($request);
                }

                $message = trans("common.error.api_key_invalid");
                $errors = ["api_key" => trans("common.error.api_key_invalid")];

                return $this->sendResponse(Constants::HTTP_UNAUTHORIZED, $message, null, $errors);
            }

            $message = trans("common.error.api_key_missing_msg");
            $errors = ["api_key" => trans("common.error.api_key_missing_err")];

            return $this->sendResponse(Constants::HTTP_UNAUTHORIZED, $message, null, $errors);
        }

        return $next($request);
    }
}
