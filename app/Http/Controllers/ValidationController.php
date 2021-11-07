<?php

namespace App\Http\Controllers;

use App\Rules\ValidPrivateConnectRule;
use App\Rules\ValidPublicConnectRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ValidationController extends Controller
{
    public function __invoke(Request $request, $entity)
    {
        $ruleLookup = [
            'host' => [
                'name'           => [
                    'sometimes',
                    Rule::unique('hosts', 'name')
                        ->where('team_id', current_team()->id)
                        ->ignore($request->ignore),
                ],
                'publicConnect'  => [
                    'sometimes',
                    new ValidPublicConnectRule,
                ],
                'privateConnect' => [
                    'sometimes',
                    new ValidPrivateConnectRule,
                ],
            ],
            'user' => [
                'email'           => [
                    'sometimes',
                    Rule::unique('users', 'email')
                        ->where('team_id', current_team()->id)
                        ->ignore($request->ignore),
                ],
            ],
        ];

        $validator = validator($request->except('ignore'), $ruleLookup[$entity]);

        $data = collect($request->except('ignore'))->map(function ($item, $key) use ($validator) {
            if ($validator->errors()->has($key)) {
                return [
                    'valid' => false,
                    'data'  => [
                        'message' => $validator->errors()->first($key),
                    ],
                ];
            } else {
                return [
                    'valid' => true,
                    'data'  => [
                        'message' => null,
                    ],
                ];
            }
        });

        return $this->success(compact('data'));
    }
}
