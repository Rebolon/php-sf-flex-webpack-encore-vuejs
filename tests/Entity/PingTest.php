<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests\Entity;

use App\Tests\Common\ApiAbstract;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Class PingTest
 * @package App\Tests\Entity
 */
class PingTest extends ApiAbstract
{
    public $uriGetItem;
    public $uriGetCollection;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->uriGetItem = $this->getRouter()->generate('api_pings_get_item', ['id' => 1, ]);
        $this->uriGetCollection = $this->getRouter()->generate('api_pings_get_collection', []);
    }

    /**
     * @group git-pre-push
     */
    public function testPing()
    {
        $headers = $this->prepareHeaders($this->headers);
        $errMsg = sprintf("route: %s", $this->uriGetCollection.'.json');

        $this->client->request('GET', $this->uriGetCollection, [], [], $headers);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $errMsg);
        $json = $this->client->getResponse()->getContent();
        $jsonDecoded = json_decode($json);
        $this->assertJson($json);
        $this->assertCount(2, $jsonDecoded);
        $this->assertPropsFromJson('Ping', $jsonDecoded[0]);

        $errMsg = sprintf("route: %s", $this->uriGetItem.'.json');

        $this->client->request('GET', $this->uriGetItem, [], [], $headers);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $errMsg);
        $json = $this->client->getResponse()->getContent();
        $jsonDecoded = json_decode($json);
        $this->assertJson($json);
        $this->assertPropsFromJson('Ping', $jsonDecoded);
    }
}
