<?php

namespace App\Http\Requests\Team;

use App\Enums\TeamMemberRole;
use App\Rules\EmailRule;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTeamMemberRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email'           => [
                'required',
                new EmailRule,
                Rule::unique('users', 'email'),
            ],
            'createRecipient' => [
                'required',
                'boolean',
            ],
            'role'            => [
                'required',
                new EnumValue(TeamMemberRole::class),
            ],
        ];
    }
}
