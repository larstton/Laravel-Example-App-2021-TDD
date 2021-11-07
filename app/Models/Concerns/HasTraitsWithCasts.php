<?php

namespace App\Models\Concerns;

trait HasTraitsWithCasts
{
    public function getCasts()
    {
        $class = static::class;

        foreach (class_uses_recursive($class) as $trait) {
            if (method_exists($class, $method = 'get'.class_basename($trait).'Casts')) {
                $this->casts = array_merge($this->casts, $this->{$method}());
            }
        }

        return parent::getCasts();
    }
}
