<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class WarehouseController extends Controller
{
    // Warehouse return list removed. Stub to avoid missing class errors.
    public function __call($method, $parameters)
    {
        abort(404);
    }
}
