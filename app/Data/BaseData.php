<?php

namespace App\Data;

use Closure;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\DataTransferObject;

class BaseData extends DataTransferObject
{
    private Collection $hasData;

    public static function nullableIntCast($value): ?int
    {
        return is_null($value) ? null : (int) $value;
    }

    public static function nullableJsonCast($value): ?array
    {
        return is_null($value) ? null : json_decode($value, true);
    }

    public static function nullableBoolCast($value): ?bool
    {
        return is_null($value) ? null : (bool) $value;
    }

    public static function nullableFloatCast($value): ?float
    {
        return is_null($value) ? null : (float) $value;
    }

    /**
     * @param string $key
     * @param  null|Closure  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if ($this->hasData($key) && property_exists($this, $key)) {
            return $this->$key;
        }

        return value($default);
    }

    public function hasData($key): bool
    {
        return $this->hasData->get($key, true);
    }

    public function setHasData(array $hasData): self
    {
        $this->hasData = collect($hasData)
            ->filter(fn ($_, $key) => property_exists($this, $key))
            ->map(fn ($value) => (bool) $value);

        return $this;
    }
}
