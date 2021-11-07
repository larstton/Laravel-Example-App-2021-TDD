<?php

namespace App\Support\Tenancy\Facades;

use App\Support\Tenancy\Contracts\IsTenant;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin \App\Support\Tenancy\TenantManager
 * @method static void guard()
 * @method static bool isEnabled()
 * @method static null|IsTenant getCurrentTenant()
 * @method static null|IsTenant setCurrentTenant(?IsTenant $tenant)
 * @method static void enableTenancyChecks()
 * @method static void disableTenancyChecks()
 */
class TenantManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tenant-manager';
    }
}
