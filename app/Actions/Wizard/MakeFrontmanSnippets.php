<?php

namespace App\Actions\Wizard;

use App\Models\Frontman;

class MakeFrontmanSnippets
{
    public function execute(Frontman $frontman)
    {
        $config = config('install-snippets.frontman');

        $params = [
            'BASE_URL'    => __($config['base_url'], ['VERSION' => $config['version']]),
            'HUB_URL'     => $config['hub_url'],
            'VERSION'     => $config['version'],
            'FRONTMAN_ID' => $frontman->id,
            'PASSWORD'    => $frontman->password,
        ];

        return collect(config('install-snippets.frontman.conf'))->map(function ($item) use ($params) {
            foreach ($item as &$string) {
                $string = __($string, $params);
            }

            return $item;
        })->all();
    }
}
