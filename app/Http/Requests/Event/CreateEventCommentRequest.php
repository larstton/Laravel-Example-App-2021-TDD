<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventCommentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'visibleToGuests' => [
                'bool',
            ],
            'statuspage'      => [
                'bool',
            ],
            'forward'         => [
                'bool',
            ],
            'text'            => [
                'required',
                'string',
                'max:1000',
            ],
        ];
    }
}
