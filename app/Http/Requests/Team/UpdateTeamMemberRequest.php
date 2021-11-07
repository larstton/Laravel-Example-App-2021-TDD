<?php

namespace App\Http\Requests\Team;

use App\Enums\TeamMemberRole;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamMemberRequest extends FormRequest
{
    public function rules()
    {
        return [
            'role'    => [
                'sometimes',
                new EnumValue(TeamMemberRole::class),
            ],
            'subUnit' => [
                'nullable',
                'sometimes',
                Rule::exists('sub_units', 'id')->where('team_id', current_team()->id),
            ],
            'hostTag' => [
                'nullable',
                'sometimes',
                'string',
            ],
        ];
    }
}
