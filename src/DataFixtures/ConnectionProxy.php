<?php
namespace App\DataFixtures;

use Doctrine\DBAL\Connection;

class ConnectionProxy
{
    /**
     * @var array of Connection
     */
    protected $connections = [];

    /**
     * ConnectionProxy constructor.
     *
     * @param Connection $mainDbalConnection
     * @param Connection $fixturesDbalConnection
     */
    public function __construct(Connection $mainDbalConnection, Connection $fixturesDbalConnection)
    {
        $this->connections['main'] = $mainDbalConnection;
        $this->connections['fixtures'] = $fixturesDbalConnection;
    }

    /**
     * @return Connection
     */
    public function getMain()
    {
        return $this->connections['main'];
    }

    /**
     * @return Connection
     */
    public function getFixtures()
    {
        return $this->connections['fixtures'];
    }
}
