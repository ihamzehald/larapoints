<?php
namespace App\Http\Controllers\API\V1;

use \App\Http\Controllers\API\V1\APIController;
use App\Http\Helpers\Constants;

class UserController extends APIController{

    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware("auth:api_jwt");
    }

    /**
     * Get a user profile
     *
     * @return mixed
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Get(
     *   path="/user/me",
     *   tags={"User"},
     *   summary="Get the authenticated User",
     *   description="Get the authenticated User",
     *   operationId="UserMe",
     *  @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         description="Response data",
     *                         ref="#/components/schemas/User"
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="null",
     *                         description="response errors",
     *                     ),
     *                     example={
     *                         "message": "JWT token refresh successfully",
     *                         "data": {
     *                                      "id": 1,
     *                                      "name": "Hamza al darawsheh",
     *                                      "email": "ihamzehald@gmail.com",
     *                                      "email_verified_at": null,
     *                                      "created_at": "2020-03-20T09:10:32.000000Z",
     *                                      "updated_at": "2020-05-08T20:39:06.000000Z"
     *                                  },
     *                         "errors": null
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *    @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="null",
     *                         description="Response data",
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         description="response errors",
     *                         @OA\Property(
     *                              property="unauthorized",
     *                              type="string",
     *                              description="Unauthorized error message",
     *                          ),
     *                     ),
     *                     example={
     *                         "message": "Unauthorized",
     *                         "data": null,
     *                         "errors": {
     *                                      "unauthorized": "Unauthorized request"
     *                                  }
     *
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     security={
     *         {"bearerJWTAuth": {}}
     *     }
     * )
     */

    public function me()
    {
        $message = "User profile returned successfully";
        $data = auth("api_jwt")->user();

        return $this->sendResponse(Constants::HTTP_SUCCESS, $message, $data);
    }

}
