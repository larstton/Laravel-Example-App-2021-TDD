<?php

namespace App\Console\Commands\Database\Connectors;

interface Connector
{
    public function exec(string $sql);
}
