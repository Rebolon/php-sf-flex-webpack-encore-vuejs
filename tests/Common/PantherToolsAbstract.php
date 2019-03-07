<?php

namespace App\Tests\Common;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class PantherToolsAbstract extends PantherTestCase
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var
     */
    protected $client;

    /**
     * @var string
     */
    protected $testLogin;

    /**
     * @var string
     */
    protected $testPwd;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $dbCon;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public static function getOutPut()
    {
        return new ConsoleOutput(ConsoleOutput::VERBOSITY_VERBOSE);
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // define globally the port
        $_SERVER['PANTHER_WEB_SERVER_PORT'] = 9010;

        $kernel = static::bootKernel();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:database:drop',
            '--force' => true,
        ]);
        $output = static::getOutPut();
        $application->run($input, $output);

        $input = new ArrayInput([
            'command' => 'doctrine:database:create',
        ]);
        $output = static::getOutPut();
        $application->run($input, $output);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->testLogin = 'test_js';
        $this->testPwd = 'test';

        $kernel = static::bootKernel();
        $client = self::createClient();

        // mock useless class
        $this->logger = $this->createMock('\Psr\Log\LoggerInterface');

        // reuse service
        $this->dbCon = $client->getContainer()->get('database_connection');
        $this->em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'doctrine:schema:create',
        ]);
        $output = static::getOutPut();
        $application->run($input, $output);

        // @todo don't understand why db is not filled
        $input = new ArrayInput([
            'command' => 'doctrine:fixtures:load',
            '--no-interaction' => true,
        ]);
        $application->run($input, $output);
    }

    public function tearDown()
    {
        parent::tearDown();

        $kernel = static::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:schema:drop',
            '--force' => true,
        ]);
        $output = static::getOutPut();
        $application->run($input, $output);
    }

    /**
     * @return Router
     */
    protected function getRouter()
    {
        if (!$this->router) {
            $this->router = static::$kernel->getContainer()->get("router");
        }

        return $this->router;
    }
}
