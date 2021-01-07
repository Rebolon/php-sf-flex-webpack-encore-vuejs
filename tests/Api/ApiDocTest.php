<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests\Api;

use App\Tests\Common\ApiAbstract;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Quick test on all login pages
 * @package App\Tests\Api
 */
class ApiDocTest extends ApiAbstract
{
    public $uriLogin;
    public $uriSecured;
    public $token;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->uriLogin = $this->getRouter()->generate('api_login_check', []);
        $this->uriSecured= $this->getRouter()->generate('api_ping_secureds_get_collection', []);
    }

    /**
     * @group git-pre-push
     */
    public function testApiDocISAvailable()
    {
        $errMsg = sprintf("route: %s", $this->uriSecured);
        $headers = $this->prepareHeaders($this->headers);

        $uri = $this->router->generate('api_doc', ['format' => 'json', 'spec_version' => 3, ], Router::NETWORK_PATH);
        $client = $this->client;
        $client->request(
            'GET',
            $uri,
            ['headers' => $headers]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);
        $this->assertStringStartsWith('application/ld+json', $client->getResponse()->getHeaders()['content-type'][0], $errMsg);
    }
}
