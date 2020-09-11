<?php
namespace App\Http\Controllers\API\V2;

use \App\Http\Controllers\API\V1\UserController as UserControllerV1;
use Carbon\Carbon;
use App\Http\Helpers\Constants;

/**
 * Override the extended UserControllerV1 methods to change their behaviour in v2
 */

class UserController extends UserControllerV1
{



    /**
     * Example of changing /user/me behaviour in V2 by adding new attributes
     */

    public function me()
    {
        $message = trans("common.success.generic");
        $data = auth("api_jwt")->user();

        // Adding a new attribute in v2 that is not in V1

        $data["created_at_human"] =  Carbon::parse($data->created_at)->diffForHumans();

        return $this->sendResponse(Constants::HTTP_SUCCESS, $message, $data);
    }
}
