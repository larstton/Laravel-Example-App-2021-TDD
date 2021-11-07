<?php

namespace App\Console\Commands\Database\Connectors;

class DryRunConnector implements Connector
{
    public function __construct($output)
    {
        $this->output = $output;
    }

    public function exec(string $sql)
    {
        return $this->output->writeln(sprintf(
            '<info>[DRY RUN] %s</info>',
            $sql
        ));
    }
}
