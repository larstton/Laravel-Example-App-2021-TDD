<?php

namespace Tests\Concerns;

use App\Support\Tenancy\Facades\TenantManager;

trait WithoutTenancyChecks
{
    public function disableTenancyChecksForAllTests()
    {
        TenantManager::disableTenancyChecks();
    }
}
