<?php

namespace App\Http\Transformers;

use Illuminate\Support\Arr;

class IntegromatDataTransformer
{
    /**
     * converts data from [ { "key": "key1",  "value": "some value" }, { "key": "email", "value": "anton@gribanov.info" }, { "key": "email", "value": "sveta@gribanov.info" }, { "key": "email", "value": "kirov@gribanov.info"}]
     * to {"key1": "some value", "email": ["anton@gribanov.info", "sveta@gribanov.info", "kirov@gribanov.info"]}.
     * @param $input
     * @return array
     */
    public static function fromRequest($input)
    {
        $result = collect($input)->mapToGroups(function ($item, $key) {
            //group values into ["key"=>["value1","value2"]]
            return [$item['key'] => $item['value']];
        })->mapWithKeys(function ($item, $key) {
            //if group has 1 value only - unwrap it from array
            return [$key => count($item) == 1 ? collect($item)->first() : $item];
        });

        return $result->toArray();
    }

    /**
     * converts data from {"key1": "some value", "email": ["anton@gribanov.info", "sveta@gribanov.info", "kirov@gribanov.info"]}
     * to [ { "key": "key1",  "value": "some value" }, { "key": "email", "value": "anton@gribanov.info" }, { "key": "email", "value": "sveta@gribanov.info" }, { "key": "email", "value": "kirov@gribanov.info"}].
     * @param $input
     * @return mixed
     */
    public static function forFrontend($input)
    {
        $input = collect($input)->mapWithKeys(function ($item, $key) {
            return [$key => Arr::wrap($item)];
        });

        return $input->keys()->reduce(function ($acc, $key) use ($input) {
            $mapped = collect($input->get($key))->map(function ($item) use ($key) {
                return [
                    'key'   => $key,
                    'value' => $item,
                ];
            })->all();
            $acc = array_merge($acc, $mapped);

            return $acc;
        }, []);
    }
}
