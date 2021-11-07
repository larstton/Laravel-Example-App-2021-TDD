<?php

namespace App\Http\Api\V1\Controllers;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function __invoke()
    {
        return response()
            ->json('Welcome to the CloudRadar API. Refer to our documentation on https://docs.cloudradar.io/api');
    }
}
