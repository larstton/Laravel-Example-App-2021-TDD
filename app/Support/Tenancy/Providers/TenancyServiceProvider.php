<?php

namespace App\Support\Tenancy\Providers;

use App\Support\Tenancy\Actions\MakeQueueTenantAwareAction;
use App\Support\Tenancy\TenantManager;
use Illuminate\Support\ServiceProvider;

class TenancyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(TenantManager::class, fn () => new TenantManager);
        $this->app->alias(TenantManager::class, 'tenant-manager');
    }

    public function boot()
    {
        $this->app->make(MakeQueueTenantAwareAction::class)->execute();
    }
}
