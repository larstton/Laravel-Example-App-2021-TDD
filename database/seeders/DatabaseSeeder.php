<?php

namespace Database\Seeders;

use App\Support\Tenancy\Facades\TenantManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        if (app()->environment('production')) {
            // Just to be extra safe 😃
            return;
        }

        TenantManager::disableTenancyChecks();
        Model::unguard();

        switch (app()->environment()) {
            case 'testing':
            case 'local':
                $this->call([
                    FrontmanSeeder::class,
                ]);
                break;
        }
    }
}
