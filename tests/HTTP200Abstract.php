<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DomCrawler\Crawler;
use PHPUnit\Util\Printer;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

abstract class HTTP200Abstract extends WebTestCase
{
    /**
     * @var Router
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

    static public function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $kernel = static::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'doctrine:database:drop',
        ));
        $output = new NullOutput();
        $application->run($input, $output);

        $input = new ArrayInput(array(
            'command' => 'doctrine:database:create',
        ));
        $output = new NullOutput();
        $application->run($input, $output);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->testLogin = 'test';
        $this->testPwd = 'test';

        $kernel = static::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:create',
        ));
        $output = new NullOutput();
        $application->run($input, $output);

        // @todo don't understand why db is not filled
        $input = new ArrayInput(array(
            'command' => 'doctrine:fixtures:load',
        ));
        $application->run($input, $output);
    }

    public function tearDown()
    {
        parent::tearDown();

        $kernel = static::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:drop',
        ));
        $output = new NullOutput();
        $application->run($input, $output);
    }

    /**
     * @param array $options
     * @param array $server
     * @return mixed|Client
     */
    protected static function createClient(array $options = array(), array $server = array())
    {
        $env = getenv();

        $options = array_merge(['debug' => false], $options);

        // when launched by npm
        if (array_key_exists('npm_package_config_server_host_web', $env)) {
            $server = array_merge([
                'HTTP_HOST' => $env['npm_package_config_server_host_web'] . ':' . $env['npm_package_config_server_port_web'],
            ], $server);
        }

        $client = parent::createClient($options, $server);

        return $client;
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

    /**
     * @return Client;
     */
    protected function getClient()
    {
        // is it a good idea to use the same client on
        if (!$this->client) {
            $this->client = static::createClient();
            $this->client->followRedirects(true);
        }

        $this->client->restart();

        return $this->client;
    }

    /**
     * @return Crawler
     */
    protected function doLogin(Client $client)
    {
        $uri = $this->router->generate('demo_login_standard', [], Router::NETWORK_PATH);
        $crawler = $client->request('GET', $uri);
        $buttonCrawlerNode = $crawler->selectButton('login');
        $form = $buttonCrawlerNode->form(array(
            'login_username' => $this->testLogin,
            'login_password' => $this->testPwd,
        ));
        $crawler = $client->submit($form);

        return $crawler;
    }

    /**
     * Test SEO basic fields
     *
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param string $errMsg
     * @return mixed
     */
    protected function checkSEO(Crawler $crawler, $errMsg)
    {
        $filter = $crawler->filter('head title');
        $this->assertCount(1, $filter, $errMsg);
        $this->assertNotEmpty($filter->text(), $errMsg);

        return $crawler;
    }

    /**
     * Test SEO basic fields
     *
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param string $errMsg
     * @return mixed
     */
    protected function checkHeader(Crawler $crawler, $errMsg)
    {
        $filter = $crawler->filter('body nav h1');
        $this->assertCount(1, $filter, $errMsg);
        $this->assertEquals(
            <<<HTML
<a href="/"><u>Demo of symfony 4</u></a>
                <span style="font-size: smaller">with flex,
                    <span style="font-size: smaller">and webpack/encore,
                        <span style="font-size: smaller">VueJS,
                            <span style="font-size: smaller">ApiPlatform,
                                <span style="font-size: smaller">HttpPlug,...</span>
                            </span>
                        </span>
                    </span>
                </span>
            
HTML
            ,
            $filter->html(),
            $errMsg
        );

        return $crawler;
    }

    /**
     * Check the login page :
     *   * standard display with the header
     *   * form login with wrong credentials : should return to the same page with 'Identification' ribbon
     *   * form login with good credentials : should go to 'Liste des Dirigeants'
     *
     * @param $client
     * @param $uri
     * @return mixed
     */
    protected function checkLogin($client, $uri)
    {
        $errMsg = sprintf("route: %s", $uri);
        $crawler = $client->request('GET', $uri);
        $body = $crawler->filter('body');

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);

        $this->assertEquals(1, $body->count(), $errMsg);
        $this->assertNotEquals("", trim($body->text()), $errMsg);

        $this->checkSEO($crawler, $errMsg);
        $this->checkHeader($crawler, $errMsg);

        $form = $crawler->selectButton('login')->form();
        $form->setValues(array(
            'login_username' => $this->testLogin,
            'login_password' => 'fake',
        ));
        $crawler = $client->submit($form);
        $bc = $crawler->filter('body div.alert');
        $this->assertContains('Invalid credentials.', trim($bc->text()));

        $crawler = $this->doLogin($client);
        $bc = $crawler->filter('body div.container');
        $text = trim($bc->text());
        $this->assertContains('Hello Test', $text);
        $this->assertContains('You are in', $text);

        return $crawler;
    }

    protected function checkStandardRoutes()
    {

    }

    /**
     * @param Router $router
     * @param Client $client
     */
    protected function checkPages($router, $client)
    {
        $demoRoutes['simple controller'] = ['uri'=> $router->generate('simple'), ];
        $demoRoutes['hello controller with twig'] = ['uri'=> $router->generate('app_hello_world', ['uri'=> 'world', ]), ];
        $demoRoutes['httpplug demo'] = ['uri'=> $router->generate('app_httpplug_call'), ];

        $demoRoutes['symfony secured page with standard login'] = ['uri'=> $router->generate('demo_secured_page'), ];
        $demoRoutes['vuejs secured page with json login'] = ['uri'=> $router->generate('app_loginjson_index'), ];

        $demoRoutes['vuejs page with vue-router'] = ['uri'=> $router->generate('app_vuejs_index'), ];
        $demoRoutes['vuejs with quasar and vue-router'] = ['uri'=> $router->generate('app_quasar_index'), ];
        $demoRoutes['vuejs with quasar with a more complex app'] = ['uri'=> $router->generate('app_form_index'), ];

        $demoRoutes['csrf token generation'] = ['uri'=> $router->generate('token'), ];
        $demoRoutes['user login check for js app'] = ['uri'=> $router->generate('demo_secured_page_is_logged_in'), 'statusCode' => 401, ];

        $demoRoutes['api-platform: rest'] = ['uri'=> $router->generate('api_entrypoint'), ];
        $demoRoutes['api-platform: graphql'] = ['uri'=> $router->generate('api_graphql_entrypoint'), ];
        $demoRoutes['api-platform: admin react'] = ['uri'=> $router->generate('app_apiplatformadminreact_index'), ];
        $demoRoutes['easy admin'] = ['uri'=> $router->generate('admin'), ];

        foreach ($demoRoutes as $routeInfos) {
            $headers = [];
            
            if (array_key_exists('headers', $routeInfos)) {
                $headers = array_merge($headers, $routeInfos['headers']);
                foreach ($headers as $keys => $value) {
                    $prefix = 'HTTP_';
                    if (strpos($keys, $prefix) === 0) {
                        continue;
                    }

                    $headers[$prefix . $keys] = $value;
                    unset($headers[$keys]);
                }
            }

            $uri = $routeInfos['uri'];

            $errMsg = sprintf("route: %s, headers: %s", $uri, json_encode($headers));

            $crawler = $client->request('GET', $uri, [], [], $headers);

            $this->assertEquals(array_key_exists('statusCode', $routeInfos) ? : 200, $client->getResponse()->getStatusCode(), $errMsg);
        }
    }

    /**
     * @param Router $router
     * @param Client $client
     */
    protected function checkApi($router, $client)
    {
        $routesName = $this->getApiRoutes($router);

        $o = new Printer();

        $token = '';
        foreach ($routesName as $routeInfos) {
            $headers = [];
            if ($token) {
                $headers['HTTP_Authorization'] = $token;
            }

            $routeName = $routeInfos;
            if (is_array($routeInfos)) {
                if (array_key_exists('headers', $routeInfos)) {
                    $headers = array_merge($headers, $routeInfos['headers']);
                    foreach ($headers as $keys => $value) {
                        $prefix = 'HTTP_';
                        if (strpos($keys, $prefix) === 0) {
                            continue;
                        }

                        $headers[$prefix . $keys] = $value;
                        unset($headers[$keys]);
                    }
                }

                $routeName = $routeInfos['uri'];
            }
            $uri = "http:" . $routeName;

            // $o->write(PHP_EOL.$uri.PHP_EOL);
            $errMsg = sprintf("route: %s, headers: %s", $uri, json_encode($headers));

            $crawler = $client->request('GET', $uri, [], [], $headers);

            // @TODO check contentType
            $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);
            $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'), $errMsg);
        }
        $o->flush();
    }

    /**
     * @param $router
     *
     * @return array
     */
    protected function getApiRoutes(Router $router): array
    {
        $routesName = [];

        // @todo list manually routes to test and then generate them with valid parameters
        // following code is wrong coz it lacks valid params to generate routes
        foreach ($router->getRouteCollection() as $name => $route) {
            if (0 !== strpos($name,'api')) {
                continue;
            }

            $routesName[] = $router->generate($name, [], Router::NETWORK_PATH);
        }

        return $routesName;
    }
}
