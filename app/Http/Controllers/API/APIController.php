<?php


namespace App\Http\Controllers\API;

use \App\Http\Controllers\Controller;
use \Illuminate\Http\Request;

/**
 * Class APIController
 * @package App\Http\Controllers\API
 * This is the main API Controller,
 * all the API controllers should extend from this controller
 */
class APIController extends Controller
{

    public function __construct()
    {
        $this->middleware("apiKey");
    }

}