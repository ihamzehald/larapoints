<?php
namespace App\Http\Controllers\API;

use \App\Http\Controllers\Controller;

class UserController extends Controller{

    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("auth:api_jwt");
    }


    /**
     * Get the authenticated User.
     * @return \Illuminate\Http\JsonResponse
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
     *                 @OA\Schema(ref="#/components/schemas/User")
     *              )
     *         }
     *     ),
     *   @OA\Response(response="401",description="Unauthorized"),
     *  security={
     *         {"bearerJWTAuth": {}}
     *     }
     * )
     */
    public function me()
    {
        return response()->json(auth("api_jwt")->user());
    }

}
