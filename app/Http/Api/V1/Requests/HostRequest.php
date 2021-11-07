<?php

namespace App\Http\Api\V1\Requests;

use App\Models\Frontman;
use App\Rules\ValidHostConnectRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

abstract class HostRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'                       => [
                'required',
                'string',
                'min:2',
                'max:255',
                'unique' => Rule::unique('hosts', 'name')->where('team_id', current_team()->id),
            ],
            'connect'                    => [
                'required_unless:cagent,1', // optional if cagent is checked
                'nullable',
                'string',
                'min:2',
                'max:255',
                'unique' => Rule::unique('hosts', 'connect')->where('team_id', current_team()->id),
                new ValidHostConnectRule,
            ],
            'description'                => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],
            'cagent'                     => [
                'sometimes',
                'boolean',
            ],
            'dashboard'                  => [
                'sometimes',
                'boolean',
            ],
            'active'                     => [
                'sometimes',
                'boolean',
            ],
            'muted'                      => [
                'sometimes',
                'boolean',
            ],
            'frontman'                   => [
                'sometimes',
                'nullable',
                'string',
                'uuid',
                Rule::exists('frontmen', 'id')
                    ->whereIn('team_id', [
                        current_team()->id,
                        Frontman::DEFAULT_FRONTMAN_UUID,
                    ]),
            ],
            'customerUuid'               => [
                'sometimes',
                'nullable',
                'string',
                'uuid',
                Rule::exists('sub_units', 'id')->where('team_id', current_team()->id),
            ],
            'tags'                       => [
                'sometimes',
                'nullable',
                'array',
                'max:10',
            ],
            'tags.*'                     => [
                'string',
                'min:1',
                'max:30',
                'regex:/^[\w\d\-_.:\s]+$/',
            ],
            'snmpProtocol'               => [
                'sometimes',
                'nullable',
                'string',
                Rule::in(['v2', 'v3']),
            ],
            'snmpPort'                   => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],
            'snmpCommunity'              => [
                Rule::requiredIf(fn () => $this->input('snmpProtocol') === 'v2'),
                'nullable',
                'string',
                'min:1',
                'max:255',
            ],
            'snmpTimeout'                => [
                'sometimes',
                'nullable',
                'float',
                'min:0.01',
                'max:99.99',
            ],
            'snmpPrivacyProtocol'        => [
                Rule::requiredIf(
                    fn () => $this->input('snmpProtocol') === 'v3' && $this->input('snmpSecurityLevel') === 'authPriv'
                ),
                'nullable',
                'string',
                Rule::in(['des', 'aes']),
            ],
            'snmpSecurityLevel'          => [
                Rule::requiredIf(fn () => $this->input('snmpProtocol') === 'v3'),
                'nullable',
                'string',
                Rule::in(['noAuthNoPriv', 'authPriv', 'authNoPriv']),
            ],
            'snmpAuthenticationProtocol' => [
                Rule::requiredIf(
                    fn () => $this->input('snmpProtocol') === 'v3' &&
                        in_array($this->input('snmpSecurityLevel'), ['authNoPriv', 'authPriv'])
                ),
                'nullable',
                'string',
                Rule::in(['sha', 'md5']),
            ],
            'snmpUsername'               => [
                Rule::requiredIf(
                    fn () => $this->input('snmpProtocol') === 'v3' &&
                        in_array($this->input('snmpSecurityLevel'), ['authNoPriv', 'authPriv'])
                ),
                'nullable',
                'string',
                'min:1',
                'max:255',
            ],
            'snmpAuthenticationPassword' => [
                Rule::requiredIf(
                    fn () => $this->input('snmpProtocol') === 'v3' &&
                        in_array($this->input('snmpSecurityLevel'), ['authNoPriv', 'authPriv'])
                ),
                'nullable',
                'string',
                'min:1',
                'max:255',
            ],
            'snmpPrivacyPassword'        => [
                Rule::requiredIf(
                    fn () => $this->input('snmpProtocol') === 'v3' && $this->input('snmpSecurityLevel') === 'authPriv'
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
            'connect'                    => 'FQDN / IP Address',
            'snmpProtocol'               => 'snmp protocol',
            'snmpPort'                   => 'snmp port',
            'snmpCommunity'              => 'snmp community',
            'snmpTimeout'                => 'snmp timeout',
            'snmpPrivacyProtocol'        => 'snmp privacy protocol',
            'snmpSecurityLevel'          => 'snmp security level',
            'snmpAuthenticationProtocol' => 'snmp authentication protocol',
            'snmpUsername'               => 'snmp username',
            'snmpAuthenticationPassword' => 'snmp authentication password',
            'snmpPrivacyPassword'        => 'snmp privacy password',
        ];
    }

    public function messages()
    {
        return [
            'name.unique'             => 'A host with this name already exists for your team.',
            'connect.unique'          => 'The :attribute is already being monitored in another host by your team.',
            'connect.required_unless' => 'You must supply a :attribute when not using the agent.',
            'frontman.exists'         => 'The frontman supplied does not exist or does not belong to your team.',
            'customerUuid.exists'     => 'The customer / sub-unit supplied does not exist or does not belong to your team.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $messages = collect($this->validator->errors()->messages())->flatten();
        $response = response()->json([
            'success' => false,
            'error'   => $messages->first(),
            'details' => $messages,
            'host'    => $this->all(),
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
