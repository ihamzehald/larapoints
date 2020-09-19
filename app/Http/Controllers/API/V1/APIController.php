<?php


namespace App\Http\Controllers\API\V1;

use \App\Http\Controllers\Controller;
use \Illuminate\Http\Request;

use \App\Http\Helpers\ApiResponse;
use App\Http\Helpers\Generators;
use App\Http\Helpers\Validators;

/**
 * Class APIController
 *
 * @package App\Http\Controllers\API
 *
 * This is the main API Controller,
 * all the API controllers should extend this controller
 *
 * Swagger UI documentation (OA)
 *
 * @OA\Info(
 *      version="1.0.0",
 *      title="LaraPoints",
 *      description="LaraPoints REST API skeleton documentation",
 *      @OA\Contact(
 *          email="ihamzehald@gmail.com"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="LaraPoints host"
 *  )
 *
 * @OA\SecurityScheme(
 *     @OA\Flow(
 *         flow="clientCredentials",
 *         tokenUrl="oauth/token",
 *         scopes={}
 *     ),
 *     securityScheme="bearerJWTAuth",
 *     in="header",
 *     type="http",
 *     description="Oauth2 security",
 *     name="oauth2",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 *
 */

class APIController extends Controller
{

    use Validators;
    use Generators;
    use ApiResponse;
    public $user;

    public function __construct()
    {
        $this->middleware("apiKey");
        $this->user = auth("api_jwt")->user();
    }
}
