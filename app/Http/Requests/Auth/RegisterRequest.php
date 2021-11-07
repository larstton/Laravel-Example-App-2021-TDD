<?php

namespace App\Http\Requests\Auth;

use App\Rules\EmailIsNotBanned;
use App\Rules\EmailRule;
use App\Rules\InviteCodeRule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email'            => [
                'required',
                new EmailRule,
                Rule::unique('users', 'email'),
                new EmailIsNotBanned,
            ],
            'password'         => [
                'required',
                'string',
                'min:7',
            ],
            'termsAccepted'    => [
                'required',
                'accepted',
            ],
            'privacyAccepted'  => [
                'required',
                'accepted',
            ],
            'partner'          => [
                'nullable',
                'string',
                'min:5',
                'max:80',
                'regex:/^[a-zA-z0-9.]+$/',
            ],
            'partnerExtraData' => [
                'nullable',
                'json',
                'max:1024',
            ],
            'invitationCode'   => [
                'nullable',
                'string',
                new InviteCodeRule,
            ],
            'lang'             => [
                Rule::in(['de', 'en', 'es', 'pt', 'fr']),
            ],
            'registrationTrack' => [
                'nullable',
                'sometimes',
                'json'
            ],
        ];
    }

    public function getTrialEnd(): ?Carbon
    {
        if (is_null($this->invitationCode)) {
            return null;
        }

        preg_match('/[i-z][a-h]([0-9])[a-z]([1-9])([a-z][a-z])/', $this->invitationCode, $match);

        $trialDurationInDays = (int) $match[2].$match[1];

        return now()->addDays($trialDurationInDays);
    }

    public function attributes()
    {
        return [
            'termsAccepted'   => 'terms & conditions',
            'privacyAccepted' => 'privacy policy',
        ];
    }
}
