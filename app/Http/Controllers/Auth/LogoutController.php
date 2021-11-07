<?php

namespace App\Http\Controllers\Auth;

use App\Events\Auth\UserLoggedOut;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function __invoke()
    {
        $user = $this->user();

        auth()->logout();

        event(new UserLoggedOut($user));

        return $this->accepted();
    }
}
