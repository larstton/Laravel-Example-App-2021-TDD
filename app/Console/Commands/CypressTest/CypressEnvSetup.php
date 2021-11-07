<?php

namespace App\Console\Commands\CypressTest;

class CypressEnvSetup extends CypressEnv
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cypress:env-setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup temp env for frontend Cypress test suite.';

    /**
     * Execute the console command.
     *
     * @return mixed|void
     */
    public function handle()
    {
        if (file_exists(base_path($this->appTestEnvFile()))) {
            if (file_get_contents(base_path('.env')) !== file_get_contents(base_path($this->appTestEnvFile()))) {
                $this->backupEnvironment();
            }

            $this->refreshEnvironment();
        }
    }
}
