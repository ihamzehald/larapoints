<?php


namespace App\Http\Controllers\API;

use \App\Http\Controllers\Controller;
use \Illuminate\Http\Request;

use \App\Http\Helpers\ApiResponse;
use App\Http\Helpers\Generators;
use App\Http\Helpers\Validators;

/**
 * Class APIController
 * @package App\Http\Controllers\API
 * This is the main API Controller,
 * all the API controllers should extend from this controller
 */
class APIController extends Controller
{

    use Validators;
    use Generators;
    use ApiResponse;


    public function __construct()
    {
        $this->middleware("apiKey");
    }


}