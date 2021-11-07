<?php

namespace App\Http\Api\V1\Controllers;

use App\Http\Controllers\Controller;

class PingController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'success' => true,
            'error'   => null,
            'details' => 'pong',
        ]);
    }
}
