<?php

namespace App\Actions\User;

use App\Data\User\UpdateUserData;
use App\Models\User;

class UpdateUserAction
{
    public function execute(User $user, UpdateUserData $data): User
    {
        $user->update([
            'nickname' => $data->get('nickname', $user->nickname),
            'name'     => $data->get('name', $user->name),
            'lang'     => $data->get('lang', $user->lang) ?? $user->lang,
        ]);

        return $user;
    }
}
