<?php

namespace App\Http\Requests\Event;

use App\Enums\EventReminder;
use App\Enums\EventState;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function rules()
    {
        return [
            'reminders' => [
                'nullable',
                'int',
                new EnumValue(EventReminder::class, false),
            ],
            'state'     => [
                'required',
                'int',
                new EnumValue(EventState::class, false),
            ],
        ];
    }
}
