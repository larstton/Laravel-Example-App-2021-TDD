<?php

namespace App\Http\Controllers\User;

use App\Events\User\UserUpdated;
use App\Events\User\UserUpdatedPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;

class ChangePasswordController extends Controller
{
    public function __invoke(UpdatePasswordRequest $request)
    {
        tap($user = $this->user())->update([
            'password' => $request->password,
        ]);

        UserUpdated::dispatch($user);
        UserUpdatedPassword::dispatch($user);

        return $this->noContent();
    }
}
