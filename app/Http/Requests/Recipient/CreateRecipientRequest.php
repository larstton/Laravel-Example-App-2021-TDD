<?php

namespace App\Http\Requests\Recipient;

use App\Rules\EsendexValidCredentials;
use App\Rules\UniqueRecipientRule;
use Illuminate\Support\Facades\Validator;

class CreateRecipientRequest extends RecipientRequest
{
    public function rules()
    {
        $rules = parent::rules();

        $rules['sendto'][] = new UniqueRecipientRule;

        return $rules;
    }
}
