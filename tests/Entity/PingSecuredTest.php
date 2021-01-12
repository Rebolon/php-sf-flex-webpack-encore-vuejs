<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests\Entity;

use App\Tests\Common\ApiAbstract;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Class PingSecuredTest
 * @package App\Tests\Entity
 */
class PingSecuredTest extends ApiAbstract
{
    public $uriGetItem;
    public $uriGetCollection;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->uriGetItem = $this->getRouter()->generate('api_ping_secureds_get_item', ['id' => 1, ]);
        $this->uriGetCollection = $this->getRouter()->generate('api_ping_secureds_get_collection', []);
    }

    /**
     * @group git-pre-push
     */
    public function testCheckSecurityAndShouldFail()
    {
        $headers = $this->prepareHeaders($this->headers);
        $errMsg = sprintf("route: %s", $this->uriGetCollection.'.json');
        $client = $this->client;

        $client->request('GET', $this->uriGetCollection, ['headers' => $headers]);
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), $errMsg);

        $errMsg = sprintf("route: %s", $this->uriGetItem.'.json');

        $this->client->request('GET', $this->uriGetItem, ['headers' => $headers]);
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), $errMsg);
    }

    /**
     * @group git-pre-push
     */
    public function testMustSuccess()
    {
        $headers = $this->setAuthorization($this->prepareHeaders($this->headers));
        $errMsg = sprintf("route: %s", $this->uriGetCollection.'.json');
        $client = $this->client;

        $client->request('GET', $this->uriGetCollection, ['headers' => $headers]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);
        $json = $client->getResponse()->getContent();
        $jsonDecoded = json_decode($json);
        $this->assertJson($json);
        $this->assertCount(10, $jsonDecoded->{'hydra:member'});
        foreach ($jsonDecoded->{'hydra:member'} as $item) {
            $this->assertTrue(property_exists($item, 'pong'));
        }

        $errMsg = sprintf("route: %s", $this->uriGetItem.'.json');

        $client->request('GET', $this->uriGetItem, ['headers' => $headers]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);
        $json = $client->getResponse()->getContent();
        $jsonDecoded = json_decode($json);
        $this->assertJson($json);
        $this->assertTrue(property_exists($jsonDecoded, 'pong'));
    }
}
