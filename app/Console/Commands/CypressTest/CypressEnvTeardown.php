<?php

namespace App\Console\Commands\CypressTest;

class CypressEnvTeardown extends CypressEnv
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cypress:env-teardown';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Teardown temp env for frontend Cypress test suite.';

    /**
     * Execute the console command.
     *
     * @return mixed|void
     */
    public function handle()
    {
        if (file_exists(base_path('.env.backup'))) {
            $this->restoreEnvironment();
            $this->refreshEnvironment();
        }
    }
}
