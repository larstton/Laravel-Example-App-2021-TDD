<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\RegisterUserAction;
use App\Data\Auth\UserRegisterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Support\AgentData\AgentData;
use Tymon\JWTAuth\JWTAuth;

class RegisterController extends Controller
{
    public function __invoke(
        RegisterRequest $request,
        JWTAuth $JWTAuth,
        RegisterUserAction $registerUser
    ) {
        $user = $registerUser->execute(
            UserRegisterData::fromUserRegisterRequest($request),
        );

        return $this->created([
            'token' => $JWTAuth->fromUser($user),
        ]);
    }
}
