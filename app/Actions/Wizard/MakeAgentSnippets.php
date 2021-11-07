<?php

namespace App\Actions\Wizard;

use App\Models\Host;

class MakeAgentSnippets
{
    public function execute(Host $host)
    {
        $config = config('install-snippets.cagent');

        $params = [
            'BASE_URL' => __($config['base_url'], ['VERSION' => $config['version']]),
            'WIN_URL'  => __($config['win_url'], ['VERSION' => $config['version']]),
            'HUB_URL'  => $config['hub_url'],
            'VERSION'  => $config['version'],
            'HOST_ID'  => $host->id,
            'PASSWORD' => $host->password,
        ];

        return collect(config('install-snippets.cagent.conf'))->map(function ($item) use ($params) {
            foreach ($item as &$string) {
                $string = __($string, $params);
            }

            return $item;
        })->all();
    }
}
