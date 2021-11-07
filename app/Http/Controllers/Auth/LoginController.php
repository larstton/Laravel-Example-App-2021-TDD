<?php

/** @noinspection PhpRedundantCatchClauseInspection */

namespace App\Http\Controllers\Auth;

use App\Events\Auth\UserLoggedIn;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Exception;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tymon\JWTAuth\JWTAuth;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request, JWTAuth $JWTAuth)
    {
        try {
            throw_unless(
                $token = auth()->attempt($request->only(['email', 'password'])),
                AccessDeniedHttpException::class
            );
        } catch (Exception $e) {
            fail_validation($request->email, __('auth.failed'));
        }

        event(new UserLoggedIn($this->user()));

        return $this->accepted(compact('token'));
    }
}
