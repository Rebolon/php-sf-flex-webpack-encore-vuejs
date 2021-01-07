<?php

namespace App\Tests\Common;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
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
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $kernel = static::bootKernel();

        $dbUri = getenv('DATABASE_URL');
        $dbUriPath = strtr($kernel->getProjectDir() . strtr($dbUri, ['sqlite:///%kernel.project_dir%' => '/', ]), ['//' => '/', ]);
        $dbBkpFile = $dbUriPath.'.bkp';

        if (file_exists($dbBkpFile)) {
            copy($dbBkpFile, $dbUriPath);
            static::ensureKernelShutdown();

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

        static::ensureKernelShutdown();
    }

    /**
     * Prepare each test methods:
     *  - init own properties
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->profiles = [
            ['login' => 'test_js', 'pwd' => 'test', ],
            ['login' => 'test_php', 'pwd' => 'test', ],
        ];

        $this->currentProfileIdx = 0;

        //$kernel = static::bootKernel();
        if (method_exists($this, 'createClient')) {
            $this->client = self::createClient();
        }

        // mock useless class
        $this->logger = $this->createMock('\Psr\Log\LoggerInterface');

        // reuse service
        $this->dbCon = static::$container->get('database_connection');
        $this->em = static::$container->get('doctrine.orm.entity_manager');
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
    public static function setUpAfterClass(): void
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
     * @return RouterInterface
     */
    protected function getRouter()
    {
        if (!$this->router) {
            $this->router = static::$container->get("router");
        }

        return $this->router;
    }

    /**
     * Because WebTestCase require HTTP headers to be prefixed with HTTP_
     * This methods will do it for you, for specified headers
     *
     * @param array $headers
     * @return array
     */
    protected function prepareHeaders($headers = [])
    {
        foreach ($headers as $keys => $value) {
            $prefix = 'HTTP_';
            if (strpos($keys, $prefix) === 0) {
                continue;
            }

            $headers[$prefix . $keys] = $value;
            unset($headers[$keys]);
        }

        return $headers;
    }
}
