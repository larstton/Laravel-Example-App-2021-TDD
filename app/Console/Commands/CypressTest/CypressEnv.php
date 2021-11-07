<?php

namespace App\Console\Commands\CypressTest;

use Dotenv\Dotenv;
use Illuminate\Console\Command;

abstract class CypressEnv extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed|void
     */
    abstract public function handle();

    /**
     * Restore the backed-up environment file.
     *
     * @return void
     */
    protected function restoreEnvironment()
    {
        copy(base_path('.env.backup'), base_path('.env'));
        unlink(base_path('.env.backup'));
    }

    /**
     * Backup the current environment file.
     *
     * @return void
     */
    protected function backupEnvironment()
    {
        copy(base_path('.env'), base_path('.env.backup'));
        copy(base_path($this->appTestEnvFile()), base_path('.env'));
    }

    /**
     * @return string
     */
    protected function appTestEnvFile()
    {
        if (file_exists(base_path($file = '.env.cypress.'.$this->laravel->environment()))) {
            return $file;
        }

        return '.env.cypress';
    }

    /**
     * Refresh the current environment variables.
     *
     * @return void
     */
    protected function refreshEnvironment()
    {
        Dotenv::createMutable(base_path())->load();
    }
}
