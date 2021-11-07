<?php

namespace App\Support\Influx;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InfluxQueryBuilderRequest extends Request
{
    private static $arrayValueDelimiter = ',';

    public static function fromRequest(Request $request): self
    {
        return static::createFrom($request, new self());
    }

    public function filters(): Collection
    {
        $filterParameterName = config('influx.query-builder.parameters.filter');

        $filterParts = $this->query($filterParameterName, []);

        if (is_string($filterParts)) {
            return collect();
        }

        $filters = collect($filterParts);

        return $filters->map(function ($value) {
            return $this->getFilterValue($value);
        });
    }

    protected function getFilterValue($value)
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn ($valueValue) => $this->getFilterValue($valueValue))
                ->all();
        }

        if (Str::contains($value, static::$arrayValueDelimiter)) {
            return $this->getFilterValue(explode(static::$arrayValueDelimiter, $value));
        }

        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        return $value;
    }

    public function sorts(): Collection
    {
        $sortParameterName = config('influx.query-builder.parameters.sort');

        $sortParts = $this->query($sortParameterName);

        if (is_string($sortParts)) {
            $sortParts = explode(static::$arrayValueDelimiter, $sortParts);
        }

        return collect($sortParts)->filter();
    }

    public function appends(): Collection
    {
        $appendParameterName = config('influx.query-builder.parameters.append');

        $appendParts = $this->query($appendParameterName);

        if (is_string($appendParts)) {
            $appendParts = explode(static::$arrayValueDelimiter, $appendParts);
        }

        return collect($appendParts)->filter();
    }


}
