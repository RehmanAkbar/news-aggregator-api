<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiController  extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponse;
}
