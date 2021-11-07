<?php

namespace App\Console\Commands\Database;

use App\Console\Commands\Database\Connectors\Connector;
use App\Console\Commands\Database\Connectors\DryRunConnector;
use App\Console\Commands\Database\Connectors\PDOConnector;
use App\Console\Commands\Database\Schema\Builder;
use App\Console\Commands\Database\Schema\GrammarFactory;
use Illuminate\Console\Command;

class DropDatabaseCommand extends Command
{
    protected $signature = 'db:drop 
                            {--database= : The database name to drop} 
                            {--pretend}';

    protected $description = 'Drops database.';

    public function handle(GrammarFactory $grammars)
    {
        $dryRun = (bool) $this->option('pretend');

        if ($dryRun) {
            $this->info('[PRETENDING] Running in pretend mode.');
        }

        $connection = config('database.default');
        $configs = config(sprintf('database.connections.%s', $connection));
        $configs['database'] = $this->option('database') ?: $configs['database'];

        $builder = new Builder(
            $this->makeConnector($configs, $dryRun),
            $grammars
        );

        if ($builder->dropDatabase($configs) === false) {
            $this->error('Could not drop the database.');

            return 1;
        }

        $this->info(sprintf('Database "%s" dropped successfully.', $configs['database']));

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
