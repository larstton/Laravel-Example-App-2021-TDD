<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (! isset($model->{$model->uuidColumn()}) || is_null($model->{$model->uuidColumn()})) {
                $model->{$model->uuidColumn()} = static::makeNewUuid();
            }
        });
    }

    public static function makeNewUuid(): string
    {
        return Str::lower(Str::uuid()->toString());
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    public function scopeWhereUuid($query, $uuid, $uuidColumn = null): Builder
    {
        $uuidColumn = ! is_null($uuidColumn) ? $uuidColumn : $this->uuidColumn();
        $uuid = array_map(fn ($uuid) => Str::lower($uuid), Arr::wrap($uuid));

        return $query->whereIn($uuidColumn, Arr::wrap($uuid));
    }

    public function uuidColumn(): string
    {
        return 'id';
    }
}
