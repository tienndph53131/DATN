<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class ReturnRequestController extends Controller
{
    // Return-request feature removed. All endpoints disabled.
    public function __call($method, $parameters)
    {
        abort(404);
    }
}
