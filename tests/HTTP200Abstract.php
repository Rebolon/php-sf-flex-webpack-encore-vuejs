<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use PHPUnit\Util\Printer;

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

    protected function setUp()
    {
        parent::setUp();

        $this->testLogin = 'test';
        $this->testPwd = 'test';
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
        $buttonCrawlerNode = $crawler->selectButton('_submit');
        $form = $buttonCrawlerNode->form(array(
            'username' => $this->testLogin,
            'password' => $this->testPwd,
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
        $filter = $crawler->filter('body header');
        $this->assertCount(1, $filter, $errMsg);
        $this->assertEmpty(trim($filter->text()), $errMsg);

        $filter = $crawler->filter('body nav h1');
        $this->assertCount(1, $filter, $errMsg);
        $this->assertEquals($filter->text(), 'Demo of symfony 4 with flex, and webpack/encore, VueJS, ApiPlatform, HttpPlug,...', $errMsg);

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

        $buttonCrawlerNode = $crawler->selectButton('submit');
        $form = $buttonCrawlerNode->form(array(
            'username' => $this->testLogin,
            'password' => 'fake',
        ));
        $crawler = $client->submit($form);
        $bc = $crawler->filter('body div.alert');
        $this->assertContains('Invalid credentials.', trim($bc->text()));

        $crawler = $this->doLogin($client);
        $bc = $crawler->filter('body div.container');
        $this->assertContains('Hello Test You are in', trim($bc->text()));

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
        $demoRoutes['simple controller'] = $router->generate('simple');
        $demoRoutes['hello controller with twig'] = $router->generate('app_hello_world', ['name' => 'world', ]);
        $demoRoutes['httpplug demo'] = $router->generate('app_httpplug_call');

        $demoRoutes['symfony secured page with standard login'] = $router->generate('demo_secured_page');
        $demoRoutes['vuejs secured page with json login'] = $router->generate('app_loginjson_index');

        $demoRoutes['vuejs page with vue-router'] = $router->generate('app_vuejs_index');
        $demoRoutes['vuejs with quasar and vue-router'] = $router->generate('app_quasar_index');
        $demoRoutes['vuejs with quasar with a more complex app'] = $router->generate('app_form_index');

        $demoRoutes['csrf token generation'] = $router->generate('token');
        $demoRoutes['user login check for js app'] = $router->generate('demo_secured_page_is_logged_in');

        $demoRoutes['api-platform: rest'] = $router->generate('api_entrypoint');
        $demoRoutes['api-platform: graphql'] = $router->generate('api_graphql_entrypoint');
        $demoRoutes['api-platform: admin react'] = $router->generate('app_apiplatformadminreact_index');
        $demoRoutes['easy admin'] = $router->generate('admin');

        $o = new Printer();

        $token = '';
        foreach ($demoRoutes as $routeInfos) {
            $headers = [];

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

            $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);
        }
        $o->flush();
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

        foreach ($router->getRouteCollection() as $name => $route) {
            if (false === strpos('api', $name)) {
                continue;
            }

            $routesName[] = $router->generate($name, [], Router::NETWORK_PATH);
        }

        return $routesName;
    }
}
