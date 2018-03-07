<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests;

use App\Tests\Common\ApiAbstract;

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
        $this->markTestIncomplete('need to list all api route and then test GET, at least');

        $client = $this->getClient();
        $router = $this->getRouter();

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
}
