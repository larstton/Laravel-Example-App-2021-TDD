<?php

namespace CloudRadar\LaravelSettings\Defaults;

use Illuminate\Support\Collection;

interface DefaultRepository
{
    public function get(string $key): Collection;
}
