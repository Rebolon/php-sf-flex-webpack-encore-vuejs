<?php

namespace App\Tests\Common;

use ApiPlatform\Core\Exception\RuntimeException;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
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
     * @param KernelBrowser $client
     * @param $uri
     * @return Crawler
     */
    protected function doLoginApi(KernelBrowser $client, $uri)
    {
        $headers = $this->headers;

        $user = $this->profiles[$this->currentProfileIdx];

        return $client->request(
            'POST',
            $uri,
            [],
            [],
            $headers,
            json_encode([
                static::$container->getParameter('login_username_path') => $user['login'],
                static::$container->getContainer()->getParameter('login_password_path') => $user['pwd'],
            ])
        );
    }

    /**
     * @param KernelBrowser $client
     * @return false|string
     */
    protected function doLoginJson(KernelBrowser $client)
    {
        $uri = $this->router->generate('demo_login_json_check', [], Router::NETWORK_PATH);

        $this->doLoginApi($client, $uri);

        return $client->getResponse()->getContent();
    }

    /**
     * @param KernelBrowser $client
     * @return stdClass with token property
     */
    protected function doLoginJwt(KernelBrowser $client)
    {
        $uri = $this->router->generate('api_login_check', [], Router::NETWORK_PATH);

        $this->doLoginApi($client, $uri);
        $tokenRaw = $client->getResponse()->getContent();

        return $token = json_decode($tokenRaw);
    }

    /**
     * @param $headers
     * @param $bearer
     * @return mixed
     */
    protected function setAuthorization($headers, $bearer = null)
    {
        if (!$bearer) {
            $token = $this->doLoginJwt($this->client);
            $bearer = $token->token;
        }

        $headers['Authorization'] = static::$container->getParameter('token_jwt_bearer')
            . ' ' . $bearer;

        return $headers;
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
                $routesName[$method][strtr($route->getPath(), ['.{_format}' => '', ])] = $routeName;
            }
        }

        return $routesName;
    }

    /**
     * @return string
     */
    protected function getJsonSchema()
    {
        $headers = $this->prepareHeaders($this->headers);
        $uri = $this->router->generate('api_doc', ['format' => 'json', 'spec_version' => 3, ], Router::NETWORK_PATH);
        $client = $this->client;
        $client->request(
            'GET',
            $uri,
            [],
            [],
            $headers
        );

        if (200 !== $client->getResponse()->getStatusCode()) {
            throw new RuntimeException(sprintf("Api docs unavailable, code is %d", $client->getResponse()->getStatusCode()));
        }

        return $client->getResponse()->getContent();
    }

    /**
     * @param $def
     * @return array
     */
    protected function getJsonSchemaComponentDef($def)
    {
        $response = $this->getJsonSchema();

        $json = json_decode($response, true);
        $endpointDefs = array_keys($json['components']['schemas']);

        if (!in_array($def, $endpointDefs)) {
            throw new RuntimeException(sprintf("wrong definitions for schemas %s, (%s)", $def, print_r($endpointDefs, true)));
        }

        return $json['components']['schemas'][$def];
    }

    /**
     * @param $def
     * @param $json
     */
    protected function assertPropsFromJson($def, $json)
    {
        $schemas = $this->getJsonSchemaComponentDef($def);
        $expectedProps = array_keys($schemas['properties']);

        foreach ($expectedProps as $prop) {
            if (method_exists($schemas['properties'][$prop], '$ref')) {
                $def = $schemas['properties'][$prop]['$ref'];
                $defPrefix = '#/components/schemas/';
                if (false !== strpos($schemas['properties'][$prop]['$ref'], $defPrefix)) {
                    $def = substr($schemas['properties'][$prop]['$ref'], count($defPrefix));
                }

                $this->assertPropsFromJson(
                    $def,
                    is_array($json->$prop) ? $json->$prop[0] : $json->$prop
                );
            }

            $this->assertObjectHasAttribute($prop, $json, print_r($json, true));

            if (array_key_exists('required', $schemas)
                && in_array($prop, $schemas['required'])) {
                $this->assertNotEmpty($json->$prop);
            }

            // @todo it might be possible to do type checking coz in $ref i can know if prop is integer, string...
        }
    }
}
