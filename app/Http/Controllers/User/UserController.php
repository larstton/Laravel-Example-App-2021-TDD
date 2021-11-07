<?php

namespace App\Http\Controllers\User;

use App\Actions\User\DeleteUserAction;
use App\Actions\User\UpdateUserAction;
use App\Data\User\UpdateUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::make($this->user());
    }

    public function update(UpdateUserRequest $request, UpdateUserAction $updateUserAction)
    {
        $user = $updateUserAction->execute($this->user(), UpdateUserData::fromRequest($request));

        return UserResource::make($user);
    }

    public function destroy(Request $request, DeleteUserAction $deleteUserAction)
    {
        $this->validate($request, [
            'confirmation-phrase' => [
                'required',
                'string',
                Rule::in(['yes', 'ja', 'si', 'sim']),
            ],
            'reason'              => [
                'sometimes',
                'nullable',
                'string',
            ],
        ]);

        $deleteUserAction->execute($this->user());

        auth()->invalidate();

        $this->noContent();
    }
}
