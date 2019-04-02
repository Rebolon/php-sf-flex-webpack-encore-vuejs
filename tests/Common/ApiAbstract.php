<?php

namespace App\Tests\Common;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DomCrawler\Crawler;
use PHPUnit\Util\Printer;

abstract class ApiAbstract extends ToolsAbstract
{
    /**
     * @var array
     */
    public $headers = [
        'ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @param Client $client
     * @param $uri
     * @param array $headers
     * @return Crawler
     */
    protected function doLoginApi(Client $client, $uri)
    {
        $headers = $this->headers;

        $crawler = $client->request(
            'POST',
            $uri,
            [],
            [],
            $headers,
            json_encode([
                $client->getKernel()->getContainer()->getParameter('login_username_path') => $this->testLogin,
                $client->getKernel()->getContainer()->getParameter('login_password_path') => $this->testPwd,])
        );

        return $crawler;
    }

    /**
     * @return \stdClass
     */
    protected function doLoginJson(Client $client)
    {
        $uri = $this->router->generate('demo_login_json_check', [], Router::NETWORK_PATH);

        $this->doLoginApi($client, $uri);
        $content = $client->getResponse()->getContent();

        return $content;
    }

    /**
     * @return \stdClass with token property
     */
    protected function doLoginJwt(Client $client)
    {
        $uri = $this->router->generate('app_loginjwt_newtoken', [], Router::NETWORK_PATH);

        $this->doLoginApi($client, $uri);
        $tokenRaw = $client->getResponse()->getContent();

        return $token = json_decode($tokenRaw);
    }

    /**
     * return array of routes by method like:
     *  ["GET" => ['//localhost/api/books', ], "POST" => ...]
     *
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
            if (false === strpos($route->getPath(), '/api/')
                || '/api/{index}.{_format}' === $route->getPath()
                || '/api/contexts/{shortName}.{_format}' === $route->getPath()) {
                continue;
            }

            $routerParams = [];
            $params = [];
            preg_match_all('/\{(.*?)\}/', $route->getPath(), $params);
            if (count($params)) {
                // @todo we may use https://github.com/fzaninotto/Faker to fake data, for instance w
                foreach ($params[1] as $param) {
                    switch ($param) {
                        case 'id':
                            $routerParams[$param] = 1;
                            break;
                        case '_format':
                            $routerParams[$param] = 'json';
                            break;
                        default:
                            $routerParams[$param] = 'test';
                    }

                }
            }

            $routeName = $router->generate($name, $routerParams, Router::NETWORK_PATH);
            foreach ($route->getMethods() as $method) {
                $routesName[$method][] = $routeName;
            }
        }

        return $routesName;
    }
}
