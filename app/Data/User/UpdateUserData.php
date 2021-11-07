<?php

namespace App\Data\User;

use App\Data\BaseData;
use App\Http\Requests\User\UpdateUserRequest;

class UpdateUserData extends BaseData
{
    public ?string $nickname;
    public ?string $name;
    public ?string $lang;

    public static function fromRequest(UpdateUserRequest $request): self
    {
        return (new self([
            'nickname' => $request->nickname,
            'name'     => $request->name,
            'lang'     => $request->lang,
        ]))->setHasData([
            'nickname' => $request->has('nickname'),
            'name'     => $request->has('name'),
            'lang'     => $request->has('lang'),
        ]);
    }
}
