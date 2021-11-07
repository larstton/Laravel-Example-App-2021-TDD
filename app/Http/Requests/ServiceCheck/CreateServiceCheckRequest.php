<?php

namespace App\Http\Requests\ServiceCheck;

use App\Rules\ICMPCheckUniquenessRule;
use App\Rules\ServiceCheckProtocolPortUnique;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateServiceCheckRequest extends FormRequest
{
    public function rules()
    {
        return [
            'protocol'      => [
                'required',
                'string',
                Rule::in(['tcp', 'udp', 'icmp', 'ssl']),
                new ServiceCheckProtocolPortUnique,
                new ICMPCheckUniquenessRule,
            ],
            'checkInterval' => [
                'required',
                'integer',
                'min:'.current_team()->min_check_interval ?? '60',
                'max:3600',
            ],
            'service'       => [
                'nullable',
                'string',
                Rule::in([
                    'http', 'https', 'ssh', 'imap', 'imaps', 'pop3',
                    'smtp', 'smtps', 'pop3s', 'tcp',
                    'ping', 'sip', 'iax2',
                ]),
            ],
            'port'          => [
                'sometimes',
                'integer',
                'min:1',
                'max:65535',
                new ServiceCheckProtocolPortUnique,
            ],
            'active'        => [
                'required',
                'boolean',
            ],
            'preflight'     => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
