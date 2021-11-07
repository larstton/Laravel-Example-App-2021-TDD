<?php

namespace App\Http\Requests\WebCheck;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class WebCheckRequest extends FormRequest
{
    public function rules()
    {
        return [
            'path'                => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            'protocol'            => [
                'required',
                'string',
                Rule::in(['http', 'https']),
            ],
            'port'                => [
                'nullable',
                'integer',
                'max:65535',
            ],
            'method'              => [
                'required',
                'string',
                Rule::in(['GET', 'POST', 'HEAD']),
            ],
            'expectedPattern'     => [
                'required_without:expectedHttpStatus',
                'nullable',
                'string',
                'min:1',
                'max:75',
            ],
            'expectedHttpStatus'  => [
                'required_without:expectedPattern',
                'nullable',
                'integer',
                'min:100',
                'max:599',
            ],
            'expectedPatternPresence'  => [
                'nullable',
                'string',
                Rule::in(['present', 'absent']),
            ],
            'ignoreSSLErrors'     => [
                'boolean',
            ],
            'timeOut'             => [
                'required',
                'numeric',
                'min:1',
                'max:60',
            ],
            'dontFollowRedirects' => [
                'boolean',
            ],
            'searchHtmlSource'    => [
                'boolean',
            ],
            'checkInterval'       => [
                'required',
                'integer',
                'min:'.current_team()->min_check_interval ?? '60',
                'max:3600',
            ],
            'active'              => [
                'required',
                'boolean',
            ],
            'preflight'           => [
                'nullable',
                'boolean',
            ],
            'postData'            => [
                'nullable',
                'string',
                'max:1024',
            ],
            'headers'             => [
                'nullable',
                'array',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'ignoreSSLErrors'    => 'ignore SSL errors',
            'expectedHttpStatus' => 'expected HTTP status',
        ];
    }

    public function getWebCheckHeaders(): ?array
    {
        if (is_null($headers = $this->input('headers'))) {
            return null;
        }

        $normalizedHeaders = [];
        foreach (array_keys($headers) as $key) {
            $normalizedHeaders[strtolower($key)] = $headers[$key];
        }

        return $normalizedHeaders;
    }
}
