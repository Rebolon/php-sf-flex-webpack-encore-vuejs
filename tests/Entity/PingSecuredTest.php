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
    public function setUp()
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

        $this->client->request('GET', $this->uriGetCollection, [], [], $headers);
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode(), $errMsg);
        $this->assertJson($this->client->getResponse()->getContent());


        $errMsg = sprintf("route: %s", $this->uriGetItem.'.json');

        $this->client->request('GET', $this->uriGetItem, [], [], $headers);
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode(), $errMsg);
        $this->assertJson($this->client->getResponse()->getContent());
    }

    /**
     * @group git-pre-push
     */
    public function testMustSuccess()
    {
        $headers = $this->setAuthorization($this->headers);
        $headers = $this->prepareHeaders($headers);
        $errMsg = sprintf("route: %s", $this->uriGetCollection.'.json');

        $this->client->request('GET', $this->uriGetCollection, [], [], $headers);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $errMsg);
        $json = $this->client->getResponse()->getContent();
        $jsonDecoded = json_decode($json);
        $this->assertJson($json);
        $this->assertCount(2, $jsonDecoded);
        $this->assertPropsFromJson('PingSecured', $jsonDecoded[0]);

        $errMsg = sprintf("route: %s", $this->uriGetItem.'.json');

        $this->client->request('GET', $this->uriGetItem, [], [], $headers);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), $errMsg);
        $json = $this->client->getResponse()->getContent();
        $jsonDecoded = json_decode($json);
        $this->assertJson($json);
        $this->assertPropsFromJson('PingSecured', $jsonDecoded);
    }
}
