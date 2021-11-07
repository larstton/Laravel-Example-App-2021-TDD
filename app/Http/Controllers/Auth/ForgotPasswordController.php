<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request)
    {
        if (! $user = User::where('email', $request->input('email'))->first()) {
            // Return an ok message so frontend shows success, but no email will be sent.
            return $this->accepted();
        }

        $response = $this->broker()->sendResetLink($request->only('email'));

        if ($response !== Password::RESET_LINK_SENT) {
            $this->errorInternal();
        }

        return $this->accepted();
    }

    /**
     * @return PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }
}
