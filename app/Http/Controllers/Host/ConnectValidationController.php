<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\Host\HostConnectValidationRequest;

class ConnectValidationController extends Controller
{
    public function __invoke(HostConnectValidationRequest $request)
    {
        // All the work here is handled by the FormRequest validation :)
        return $this->noContent();
    }
}
