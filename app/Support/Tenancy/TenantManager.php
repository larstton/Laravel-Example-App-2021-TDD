<?php

namespace App\Support\Tenancy;

use App\Support\Tenancy\Contracts\IsTenant;
use App\Support\Tenancy\Exceptions\MissingTenancy;

class TenantManager
{
    private $active = true;
    private $current = null;

    public function guard()
    {
        if (! $this->isEnabled()) {
            return;
        }

        $noActiveTenant = is_null($this->getCurrentTenant());
        throw_if($noActiveTenant, MissingTenancy::make());
    }

    public function isEnabled(): bool
    {
        return (bool) $this->active;
    }

    public function getCurrentTenant(): ?IsTenant
    {
        return $this->current;
    }

    public function setCurrentTenant(?IsTenant $tenant)
    {
        $this->current = $tenant;
    }

    public function enableTenancyChecks()
    {
        $this->active = true;
    }

    public function disableTenancyChecks()
    {
        $this->active = false;
    }

    public function __call($method, $parameters)
    {
        return optional($this->getCurrentTenant())->$method($parameters);
    }

    public function __get($property)
    {
        return optional($this->getCurrentTenant())->$property;
    }
}
