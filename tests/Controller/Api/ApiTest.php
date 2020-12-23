<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests\Controller\Api;

use App\Tests\Common\ApiAbstract;
use Exception;
use PHPUnit\Util\Printer;

/**
 * Quick test on all api endpoint that should return at least 200 OK + some other checks
 */
class ApiTest extends ApiAbstract
{
    /**
     * @group git-pre-push
     */
    public function testMain()
    {
        $client = $this->client;
        $router = $this->getRouter();

        $routesName = $this->getApiRoutes($router);

        $o = new Printer();

        $headers = $this->setAuthorization($this->headers);
        $headers = $this->prepareHeaders($headers);

        // only test GET for instance
        $method = 'GET';
        foreach ($routesName[$method] as $routePath => $routeInfos) {
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

            $client->request($method, $uri, [], [], $headers);

            $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);
            $this->assertContains('application/json', $client->getResponse()->headers->get('content-type'), $errMsg);
            $json = json_decode($client->getResponse()->getContent());

            $schemas = json_decode($this->getJsonSchema(), true);
            $availablePaths = array_keys($schemas['paths']);

            $this->assertContains($routePath, $availablePaths);

            if (is_array($json)) {
                $def = $schemas['paths'][$routePath][strtolower($method)]['responses']['200']['content']['application/json']['schema']['items']['$ref'];
            } else {
                $def = $schemas['paths'][$routePath][strtolower($method)]['responses']['200']['content']['application/json']['schema']['$ref'];
            }

            $defPrefix = '#/components/schemas/';
            if (false !== strpos($def, $defPrefix)) {
                $def = substr($def, strlen($defPrefix));
            }

            $this->assertPropsFromJson($def, is_array($json) ? $json[0] : $json);
        }
        $o->flush();
    }
}
