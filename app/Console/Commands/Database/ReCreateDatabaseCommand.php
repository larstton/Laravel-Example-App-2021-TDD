<?php

namespace App\Console\Commands\Database;

use App\Console\Commands\Database\Connectors\Connector;
use App\Console\Commands\Database\Connectors\DryRunConnector;
use App\Console\Commands\Database\Connectors\PDOConnector;
use App\Console\Commands\Database\Schema\Builder;
use App\Console\Commands\Database\Schema\GrammarFactory;
use Illuminate\Console\Command;

class ReCreateDatabaseCommand extends Command
{
    protected $signature = 'db:recreate 
        {--pretend}
    ';

    protected $description = 'Re-creates the currently configured database.';

    public function handle(GrammarFactory $grammars)
    {
        $dryRun = (bool) $this->option('pretend');

        if ($dryRun) {
            $this->info('[PRETENDING] Running in pretend mode.');
        }

        $connection = config('database.default');
        $configs = config(sprintf('database.connections.%s', $connection));

        $builder = new Builder(
            $this->makeConnector($configs, $dryRun),
            $grammars
        );

        $builder->recreateDatabase($configs);

        $this->info(sprintf('Database "%s" re-created successfully.', $configs['database']));

        return 0;
    }

    private function makeConnector(array $configs, bool $dryRun = false): Connector
    {
        if ($dryRun === true) {
            return new DryRunConnector($this->output);
        }

        return PDOConnector::make($configs);
    }
}
