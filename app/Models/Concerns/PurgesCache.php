<?php

/** @noinspection PhpUnusedPrivateMethodInspection */

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait PurgesCache
{
    protected static function bootPurgesCache()
    {
        collect(['created', 'updated', 'deleted', 'restored'])->each(function ($event) {
            static::registerModelEvent($event, function (Model $model) use ($event) {
                if (value($purgeableKeys = $model->getPurgeableKeys($event))->isEmpty()) {
                    return;
                }

                $purgeableKeys->each(function ($keys, $matchedOn) use ($event, $model) {
                    collect(Arr::wrap($keys))->each(function ($key) use ($matchedOn, $event, $model) {
                        logger()->info('Cache purged for Hub', [
                            'purgedKey' => $key,
                            'event'     => $event,
                            'matchedOn' => $matchedOn,
                            'class'     => class_basename($model),
                            'modelId'   => $model->id,
                        ]);

                        Cache::forget($key);
                    });
                });
            });
        });
    }

    private function getPurgeableKeys($event): Collection
    {
        return collect($this->purgeableEvents())
            ->filter(fn ($value, $key) => Str::is($key, $event));
    }

    abstract public function purgeableEvents(): array;
}
