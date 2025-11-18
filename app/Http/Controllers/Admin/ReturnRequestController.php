<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ReturnRequestController extends Controller
{
    // Feature removed. Keep a stub controller to prevent fatal errors
    public function __call($method, $parameters)
    {
        abort(404);
    }
}
