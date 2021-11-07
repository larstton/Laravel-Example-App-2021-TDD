<?php

namespace App\Http\Requests\Recipient;

use App\Enums\RecipientMediaType;
use App\Rules\EmailRule;
use App\Rules\EsendexValidCredentials;
use App\Rules\MSTeamsValidUrl;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BaseRecipientRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'mediatype' => [
                'required', 'string', 'max:30',
                new EnumValue(RecipientMediaType::class),
            ],
            'sendto'    => ['required'],
            'option1'   => ['sometimes', 'boolean', 'nullable', Rule::in([false])],
        ];

        // Adjust rules based on recipient media-type, reports for e-mails,
        // sendto format for each type.
        switch ($this->input('mediatype')) {
            case 'email':
                $rules['sendto'] = array_merge($rules['sendto'], ['string', new EmailRule]);
                break;
            case 'phonecall':
            case 'sms':
                $rules['sendto'] = array_merge(
                    $rules['sendto'],
                    ['string', 'regex:/^\\+[0-9]+$/', 'min:5', 'max:200']
                );
                break;
            case 'pushover':
                $rules['sendto'] = array_merge(
                    $rules['sendto'],
                    ['string', 'min:30', 'max:30']
                );
                break;
            case 'slack':
                $rules['sendto'] = array_merge(
                    $rules['sendto'],
                    ['string', 'min:42', 'max:200', 'regex:/^xoxb-[0-9-]+-\w+$/im']
                );
                $rules['option1'] = ['sometimes', 'nullable', 'string', 'max:100', 'regex:/^(#|@)/'];
                break;
            case 'telegram':
                $rules['sendto'] = array_merge($rules['sendto'], ['digits_between:5,16']);
                $rules['option1'] = [
                    'sometimes', 'nullable', 'string', 'max:100',
                    'regex:/^[0-9]+:|[\s]*/',
                ];
                break;
            case 'integromat':
                $rules['sendto'] = array_merge($rules['sendto'], ['url', 'max:1024']);
                $rules['extraData'] = ['sometimes', 'nullable', 'array'];
                $rules['extraData.*.key'] = ['string'];
                $rules['extraData.*.value'] = ['string'];
                break;
            case 'msteams':
                $rules['sendto'] = array_merge($rules['sendto'], ['url', 'max:1024']);
                break;
            case 'webhook':
                $rules['sendto'] = array_merge($rules['sendto'], ['url', 'max:1024']);
                $rules['option1'] = ['sometimes', 'nullable', 'string', Rule::in(['multipart-form-data', 'json-raw'])];
                break;
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        if ('sms' === $this->input('mediatype') || 'phonecall' === $this->input('mediatype')) {
            $key = $this->input('mediatype');
            $validator->after(function ($validator) use ($key) {
                // This forces the validator to evaluate the rules defined in the rules() method above.
                if ($validator->failed()) {
                    return;
                }

                Validator::make($this->input(), [
                    'extraData.'.$key => ['array', new EsendexValidCredentials()],
                ])->validate();
            });
        }
        if ('msteams' === $this->input('mediatype')) {
            $validator->after(function ($validator) {
                // This forces the validator to evaluate the rules defined in the rules() method above.
                if ($validator->failed()) {
                    return;
                }

                Validator::make($this->input(), [
                    'sendto' => [new MSTeamsValidUrl()],
                ])->validate();
            });
        }
    }
}
