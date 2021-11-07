<?php

namespace App\Console\Commands\Database\Schema;

use App\Console\Commands\Database\Connectors\Connector;

class Builder
{
    public function __construct(Connector $connector, GrammarFactory $grammars)
    {
        $this->connector = $connector;
        $this->grammars = $grammars;
    }

    public function recreateDatabase(array $options): void
    {
        $this->dropDatabase($options);
        $this->createDatabase($options);
    }

    public function dropDatabase(array $options)
    {
        $grammar = $this->grammars->make($options['driver']);

        return $this->connector->exec(
            $grammar->compileDropDatabase($options['database'])
        );
    }

    public function createDatabase(array $options)
    {
        $grammar = $this->grammars->make($options['driver']);

        return $this->connector->exec(
            $grammar->compileCreateDatabase($options)
        );
    }
}
