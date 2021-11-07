<?php

namespace App\Http\Requests\Recipient;

use App\Enums\RecipientMediaType;
use App\Models\Recipient;
use App\Rules\EsendexValidCredentials;
use App\Rules\MSTeamsValidUrl;
use Illuminate\Support\Facades\Validator;

class SendTestMessageRequest extends BaseRecipientRequest
{
    public function rules()
    {
        $rules = parent::rules();

        //adjust rules based on recipient media-type, reports for e-mails, sendto format for each type
        switch ($this->input('mediatype')) {
            case 'email':
                $rules['sendto'] = array_merge($rules['sendto'], [
                    function ($attribute, $value, $fail) {
                        if (Recipient::where('verified', true)->where('sendto', $value)->where(
                            'media_type',
                            RecipientMediaType::Email()
                        )->doesntExist()) {
                            $fail('Your are not allowed to send test messages to unverified recipients');
                        }
                    },
                ]);
                break;
        }

        $rules['message'] = ['nullable', 'sometimes'];

        return $rules;
    }

    public function withValidator($validator)
    {
        parent::withValidator($validator);

        if ('sms' === $this->input('mediatype') || 'phonecall' === $this->input('mediatype')) {
            $key = $this->input('mediatype');
            $validator->after(function ($validator) use ($key) {
                // This forces the validator to evaluate the rules defined in the rules() method above.
                if ($validator->failed()) {
                    return;
                }
                //validate esendex credentials for test messages even if recipient is not active
                Validator::make($this->input(), [
                    'extraData.'.$key => ['array', new EsendexValidCredentials()],
                ])->validate();
            });
        }
    }
}
