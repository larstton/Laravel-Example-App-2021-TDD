<?php

namespace App\Http\Requests\Support;

use App\Enums\SupportRequestState;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSupportRequest extends FormRequest
{
    public function rules()
    {
        return [
            'state' => [
                'required',
                new EnumValue(SupportRequestState::class),
            ],
        ];
    }
}
