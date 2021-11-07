<?php

namespace CloudRadar\LaravelSettings\Defaults;

use function DeepCopy\deep_copy;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use CloudRadar\LaravelSettings\Exceptions\InvalidSettingsKey;

class FileDefaultRepository implements DefaultRepository
{
    private $entityName;

    /**
     * FileDefaultRepository constructor.
     * @param $entityName
     */
    public function __construct($entityName)
    {
        $this->entityName = $entityName;
    }

    public function get(string $key): Collection
    {
        $defaults = collect(
            $this->ensureSubSetsAreProperlyKeyed(
                $key, config($this->makeKey($key))
            )
        );

        return deep_copy($defaults);
    }

    private function ensureSubSetsAreProperlyKeyed(string $key, $defaults)
    {

        if(is_null($defaults)){
            return [];
        }
        // ensure results are fully keyed when only fetching a subset of data...
        if (Str::contains($key, '.')) {
            $key = Str::after($key, '.');
            //defaults can be boolean or empty array
            if(is_array($defaults) && !empty($defaults)) {
                foreach (Arr::dot($defaults) as $dottedKey => $value) {
                    Arr::set($defaults, $key . '.' . $dottedKey, $value);
                    Arr::forget($defaults, $dottedKey);
                }
                $defaults = array_filter($defaults);
            }else{
                $defaults =[$key => $defaults];
            }
        }

        return $defaults;
    }

    private function makeKey($key)
    {
        return config('laravel-settings.'.$this->entityName.'.config-pre-key').'-'.$key;
    }
}
