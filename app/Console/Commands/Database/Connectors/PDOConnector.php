<?php

namespace App\Console\Commands\Database\Connectors;

use PDO;

class PDOConnector implements Connector
{
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function make(array $configs): self
    {
        $host = sprintf(
            '%s:host=%s',
            $configs['driver'],
            $configs['host'] ?? '127.0.0.1'
        );

        $pdo = new PDO($host, $configs['username'], $configs['password']);

        return new static($pdo);
    }

    /**
     * @param  string  $sql
     *
     * @return mixed whatever the actual implementation returns, depending on the connector
     */
    public function exec(string $sql)
    {
        return $this->pdo->exec($sql);
    }
}
