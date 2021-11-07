<?php

namespace App\Http\Requests\Recipient;

use Illuminate\Validation\Rule;

class RecipientRequest extends BaseRecipientRequest
{
    public function rules()
    {
        $rules = parent::rules();

        $rules = array_merge($rules, [
            'description'          => ['sometimes', 'nullable', 'string', 'max:100'],
            'alerts'               => ['required', 'boolean'],
            'warnings'             => ['required', 'boolean'],
            'reminders'            => ['sometimes', 'boolean'],
            'comments'             => ['sometimes', 'boolean'],
            'recoveries'           => ['sometimes', 'boolean'],
            'dailySummary'         => ['nullable', 'boolean', Rule::in([false])],
            'dailyReports'         => ['nullable', 'boolean', Rule::in([false])],
            'weeklyReports'        => ['nullable', 'boolean', Rule::in([false])],
            'monthlyReports'       => ['nullable', 'boolean', Rule::in([false])],
            'eventUuids'           => ['nullable', 'boolean', Rule::in([false])],
            'active'               => ['sometimes', 'boolean'],
            'maximumReminders'     => ['sometimes', 'integer', 'max:240', 'min:1'],
            'reminderDelay'        => ['sometimes', 'integer', 'min:600'],
            'rules'                => ['sometimes', 'nullable'],
            'rules.operator'       => [Rule::in(['or', 'and'])],
            'rules.data'           => ['array'],
            'rules.data.*.field'   => ['string'],
            'rules.data.*.value'   => [
                function ($attribute, $value, $fail) {
                    if (! is_array($value) && ! is_string($value)) {
                        $fail($attribute.' must be a string or array of strings.');
                    }
                },
            ],
            'rules.data.*.value.*' => ['string'],
        ]);

        //adjust rules based on recipient media-type, reports for e-mails, sendto format for each type
        switch ($this->input('mediatype')) {
            case 'email':
                $rules['eventUuids'] = ['sometimes', 'boolean', 'nullable'];
                $rules['dailySummary'] = ['sometimes', 'boolean', 'nullable'];
                $rules['dailyReports'] = ['sometimes', 'boolean', 'nullable'];
                $rules['weeklyReports'] = ['sometimes', 'boolean', 'nullable'];
                $rules['monthlyReports'] = ['sometimes', 'boolean', 'nullable'];
                break;
            case 'sms':
                $rules['extraData.sms.username'] = ['required', 'string'];
                $rules['extraData.sms.password'] = ['required', 'string'];
                $rules['extraData.sms.account'] = ['required', 'string'];
                break;
            case 'phonecall':
                $rules['extraData.phonecall.username'] = ['required', 'string'];
                $rules['extraData.phonecall.password'] = ['required', 'string'];
                $rules['extraData.phonecall.account'] = ['required', 'string'];
                break;
            case 'pushover':
                $rules['extraData.pushover.priority'] = ['required', Rule::in(0, 1, 2)];
                break;
        }

        return $rules;
    }
}
