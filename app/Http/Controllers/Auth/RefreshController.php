<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class RefreshController extends Controller
{
    public function __invoke()
    {
        return $this->json([
            'token' => auth()->refresh(),
        ]);
    }
}
