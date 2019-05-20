<?php

namespace App\Tests\Common;

use Doctrine\DBAL\Connection;
use PHPUnit\Util\Printer;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\RouterInterface;

trait TestCase
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
    protected $profiles;

    /**
     * @var int
     */
    protected $currentProfileIdx;

    /**
     * @var Connection
     */
    protected $dbCon;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @return ConsoleOutput
     */
    public static function getOutPut()
    {
        return new ConsoleOutput(ConsoleOutput::VERBOSITY_VERBOSE);
    }

    /**
     * Prepare DB:
     *  - drop existing test.db
     *  - create new one
     *  - init schema
     *  - backup schema in another file
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $kernel = static::bootKernel();

        $dbUri = getenv('DATABASE_URL');
        $dbUriPath = strtr($kernel->getProjectDir() . strtr($dbUri, ['sqlite:///%kernel.project_dir%' => '/', ]), ['//' => '/', ]);
        $dbBkpFile = $dbUriPath.'.bkp';

        if (file_exists($dbBkpFile)) {
            copy($dbBkpFile, $dbUriPath);
            return;
        }

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

        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'doctrine:schema:create',
        ]);
        $output = static::getOutPut();
        $application->run($input, $output);

        $input = new ArrayInput([
            'command' => 'doctrine:fixtures:load',
            '--no-interaction' => true,
        ]);
        $application->run($input, $output);

        // backup new generated test.db into test.db.bkp to improve next class test
        copy($dbUriPath, $dbBkpFile);
    }

    /**
     * Prepare each test methods:
     *  - init own properties
     */
    protected function setUp()
    {
        parent::setUp();

        $this->profiles = [
            ['login' => 'test_js', 'pwd' => 'test', ],
            ['login' => 'test_php', 'pwd' => 'test', ],
        ];

        $this->currentProfileIdx = 0;

        $kernel = static::bootKernel();
        $this->client = self::createClient();

        // mock useless class
        $this->logger = $this->createMock('\Psr\Log\LoggerInterface');

        // reuse service
        $this->dbCon = $this->client->getContainer()->get('database_connection');
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }


    /**
     * @todo find a way to clean DB only when all test files has been run (on success or failure) to clean DB only once,
     * and not once per class
     *
     * Clean DB:
     *  - drop existing test.db
     *  - create new one
     *  - init schema
     *  - backup schema in another file
     */
    public static function setUpAfterClass()
    {
        parent::setUpBeforeClass();

        $dbUri = getenv('DATABASE_URL');
        $dbUriParsed = parse_url($dbUri);
        $dbBkpFile = $dbUriParsed['path'].'.bkp';

        if (file_exists($dbBkpFile)) {
            unlink($dbUriParsed['path'].'.bkp');

            return;
        }
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
