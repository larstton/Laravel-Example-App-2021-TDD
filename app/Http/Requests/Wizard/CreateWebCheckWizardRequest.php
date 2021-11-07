<?php

namespace App\Http\Requests\Wizard;

use App\Rules\ConnectIsNotBanned;
use Illuminate\Foundation\Http\FormRequest;

class CreateWebCheckWizardRequest extends FormRequest
{
    public function rules()
    {
        return [
            'url'       => [
                'required',
                'string',
                'url',
                'starts_with:http,https',
                new ConnectIsNotBanned,
            ],
            'preflight' => [
                'required',
                'bool',
            ],
        ];
    }
}
