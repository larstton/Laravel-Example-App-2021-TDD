<?php

namespace App\Http\Requests\Host;

use App\Models\Frontman;
use App\Rules\ConnectIsNotBanned;
use App\Rules\ValidHostConnectRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class HostRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'                        => [
                'required',
                'string',
                'min:2',
                'max:255',
                'unique' => Rule::unique('hosts', 'name')->where('team_id', current_team()->id),
            ],
            'connect'                     => [
                'required_unless:cagent,1', // optional if cagent is checked
                'nullable',
                'string',
                'min:2',
                'max:255',
                'unique' => Rule::unique('hosts', 'connect')->where('team_id', current_team()->id),
                new ValidHostConnectRule,
                new ConnectIsNotBanned,
            ],
            'description'                 => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],
            'cagent'                      => [
                'sometimes',
                'boolean',
            ],
            'dashboard'                   => [
                'sometimes',
                'boolean',
            ],
            'active'                      => [
                'sometimes',
                'boolean',
            ],
            'muted'                       => [
                'sometimes',
                'boolean',
            ],
            'frontmanId'                  => [
                'sometimes',
                'nullable',
                'string',
                'uuid',
                Rule::exists('frontmen', 'id')
                    ->whereIn('team_id', [
                        current_team()->id,
                        Frontman::DEFAULT_FRONTMAN_UUID,
                    ]),
                // new ValidFrontmanRule,
            ],
            'subUnitId'                   => [
                'sometimes',
                'nullable',
                'string',
                'uuid',
                Rule::exists('sub_units', 'id')->where('team_id', current_team()->id),
            ],
            'tags'                        => [
                'sometimes',
                'nullable',
                'array',
                'max:10',
            ],
            'tags.*'                      => [
                'nullable',
                'string',
                'min:1',
                'max:30',
                'regex:/^[\w\d\-_.:\s]+$/',
            ],
            'snmp.protocol'               => [
                'sometimes',
                'nullable',
                'string',
                Rule::in(['v2', 'v3']),
            ],
            'snmp.port'                   => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],
            'snmp.community'              => [
                Rule::requiredIf(fn () => $this->input('snmp.protocol') === 'v2'),
                'nullable',
                'string',
                'min:1',
                'max:255',
            ],
            'snmp.timeout'                => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
                'max:99',
            ],
            'snmp.privacyProtocol'        => [
                Rule::requiredIf(
                    fn () => $this->input('snmp.protocol') === 'v3' && $this->input('snmp.securityLevel') === 'authPriv'
                ),
                'nullable',
                'string',
                Rule::in(['des', 'aes']),
            ],
            'snmp.securityLevel'          => [
                Rule::requiredIf(fn () => $this->input('snmp.protocol') === 'v3'),
                'nullable',
                'string',
                Rule::in(['noAuthNoPriv', 'authPriv', 'authNoPriv']),
            ],
            'snmp.authenticationProtocol' => [
                Rule::requiredIf(
                    fn () => $this->input('snmp.protocol') === 'v3' &&
                        in_array($this->input('snmp.securityLevel'), ['authNoPriv', 'authPriv'])
                ),
                'nullable',
                'string',
                Rule::in(['sha', 'md5']),
            ],
            'snmp.username'               => [
                Rule::requiredIf(
                    fn () => $this->input('snmp.protocol') === 'v3' &&
                        in_array($this->input('snmp.securityLevel'), ['authNoPriv', 'authPriv'])
                ),
                'nullable',
                'string',
                'min:1',
                'max:255',
            ],
            'snmp.authenticationPassword' => [
                Rule::requiredIf(
                    fn () => $this->input('snmp.protocol') === 'v3' &&
                        in_array($this->input('snmp.securityLevel'), ['authNoPriv', 'authPriv'])
                ),
                'nullable',
                'string',
                'min:1',
                'max:255',
            ],
            'snmp.privacyPassword'        => [
                Rule::requiredIf(
                    fn () => $this->input('snmp.protocol') === 'v3' && $this->input('snmp.securityLevel') === 'authPriv'
                ),
                'nullable',
                'string',
                'min:1',
                'max:255',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'connect'                     => 'FQDN / IP Address',
            'snmp.protocol'               => 'snmp protocol',
            'snmp.port'                   => 'snmp port',
            'snmp.community'              => 'snmp community',
            'snmp.timeout'                => 'snmp timeout',
            'snmp.privacyProtocol'        => 'snmp privacy protocol',
            'snmp.securityLevel'          => 'snmp security level',
            'snmp.authenticationProtocol' => 'snmp authentication protocol',
            'snmp.username'               => 'snmp username',
            'snmp.authenticationPassword' => 'snmp authentication password',
            'snmp.privacyPassword'        => 'snmp privacy password',
            'tags.*'                      => 'tags',
        ];
    }

    public function messages()
    {
        return [
            'name.unique'             => 'A host with this name already exists for your team.',
            'connect.unique'          => 'The :attribute is already being monitored in another host by your team.',
            'connect.required_unless' => 'You must supply a :attribute when not using the agent.',
            'frontmanId.exists'       => 'The frontman supplied does not exist or does not belong to your team.',
            'subUnitId.exists'        => 'The sub-unit supplied does not exist or does not belong to your team.',
            'tags.*.regex'            => 'One or more of your tags contain invalid characters.',
        ];
    }
}
