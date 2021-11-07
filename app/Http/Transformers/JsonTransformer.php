<?php

namespace App\Http\Transformers;

use Illuminate\Support\Str;

class JsonTransformer
{
    public static function makeKeysCamelCase($json): ?array
    {
        if (is_null($json)) {
            return [];
        }

        $data = (array) json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return self::makeArrayKeysCamelCase($data);
    }

    public static function makeArrayKeysCamelCase(array $data): array
    {
        $return = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $return[Str::camel($key)] = self::makeArrayKeysCamelCase($value);
            } else {
                $return[Str::camel($key)] = $value;
            }
        }

        return $return;
    }
}
