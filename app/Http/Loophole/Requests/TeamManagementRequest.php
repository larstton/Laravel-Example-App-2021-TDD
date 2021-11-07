<?php

namespace App\Http\Loophole\Requests;

use App\Enums\RecipientMediaType;
use App\Enums\TeamPlan;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class TeamManagementRequest extends FormRequest
{
    public function rules()
    {
        return [
            'plan'                => [
                'required',
                'string',
                new EnumValue(TeamPlan::class),
            ],
            'maxHosts'            => [
                'required',
                'integer',
                'max:999',
            ],
            'maxRecipients'       => [
                'required',
                'integer',
                'max:100',
            ],
            'dataRetention'       => [
                'required',
                'integer',
                'max:999',
            ],
            'maxMembers'          => [
                'required',
                'integer',
                'max:99',
            ],
            'maxFrontmen'         => [
                'required',
                'integer',
                'max:999',
            ],
            'minCheckInterval'    => [
                'required',
                'integer',
            ],
            'currency'            => [
                'string',
                'nullable',
            ],
        ];
    }
}
