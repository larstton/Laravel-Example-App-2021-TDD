<?php

namespace App\Http\Queries;

use App\Support\Influx\AliasResolver;
use App\Support\Influx\Facades\Influx;
use App\Support\Influx\InfluxQueryBuilder;
use Illuminate\Validation\Rule;

class InfluxQuery extends InfluxQueryBuilder
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->defaultSorts('-time')
            ->allowedSorts('time')
            ->allowedAppends(['min','max'])
            ->allowedFilters([
                'from'     => now()->subHour()->unix(),
                'to'       => now()->unix(),
                'alias'    => [
                    'default' => null,
                    'rules'   => [
                        'sometimes',
                        'nullable',
                        'required_without:metric',
                        Rule::in(AliasResolver::getMetricAliasList()->keys()),
                    ],
                ],
                'metric'   => [
                    'default' => null,
                    'rules'   => [
                        'sometimes',
                        'nullable',
                        'required_without:alias',
                    ],
                ],
                'database' => [
                    'default' => Influx::getDefaultDatabase(),
                    'rules'   => [
                        'sometimes',
                        Rule::in([
                            'cagent',
                            'frontman',
                            'customChecks',
                            'check',
                            'customChecksResults',
                            'checkResults',
                            'cagentResults',
                            'frontmanResults',
                        ]),
                    ],
                ],
                'limit'    => [
                    'default' => null,
                    'rules'   => ['nullable', 'int'],
                ],
                'host'     => [
                    'default' => $this->request->route('host', ''),
                    'rules'   => ['required', 'uuid'],
                ],
            ]);
    }
}
