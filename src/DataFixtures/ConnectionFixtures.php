<?php

namespace App\DataFixtures;

use Doctrine\DBAL\Connection;
use RuntimeException;

class ConnectionFixtures
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * ConnectionFixtures constructor.
     *
     * @param Connection $fixturesDbalConnection
     */
    public function __construct(Connection $fixturesDbalConnection)
    {
        $this->connection = $fixturesDbalConnection;
    }

    /**
     * @return Connection
     */
    public function get()
    {
        if (!$this->connection) {
            throw new RuntimeException('Need to configure your ConnectionFixtures service');
        }

        return $this->connection;
    }
}
