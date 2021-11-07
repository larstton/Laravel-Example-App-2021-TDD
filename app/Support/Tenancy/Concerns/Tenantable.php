<?php

namespace App\Support\Tenancy\Concerns;

use App\Support\Tenancy\Facades\TenantManager;

trait Tenantable
{
    public static function hasCurrentTenant(): bool
    {
        return ! is_null(static::currentTenant());
    }

    public static function currentTenant()
    {
        $tenant = TenantManager::getCurrentTenant();

        if (is_null($tenant)) {
            return null;
        }

        return $tenant;
    }

    public function makeCurrentTenant()
    {
        /* @noinspection PhpParamsInspection */
        TenantManager::setCurrentTenant($this);
    }

    public function isCurrentTenant(): bool
    {
        return optional(static::currentTenant())->id === $this->id;
    }
}
