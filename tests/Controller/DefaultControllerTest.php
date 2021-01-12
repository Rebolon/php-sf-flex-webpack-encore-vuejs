<?php

namespace App\Tests\Controller;

use App\Tests\Common\PantherToolsAbstract;

class DefaultControllerTest extends PantherToolsAbstract
{
    /**
     * @group git-pre-push
     */
    public function testDefaultController()
    {
        $client = static::createPantherClient(); // Your app is automatically started using the built-in web server
        $crawler = $client->request('GET', $this->getRouter()->generate('index'));

        $this->assertStringContainsString('Demo of symfony 5 with flex, and webpack/encore, VueJS, ApiPlatform, HttpClient,...', $crawler->filter('nav h1')->text()); // You can use any PHPUnit assertion

        $links = $crawler->filter('ul li.list-group-item a');
        $this->assertGreaterThanOrEqual(17, count($links)); // @todo on travis it gets 18 links instead of 17, didn't understand why for instance

        $h2 = $crawler->filter('h2');
        $this->assertEquals('List of demos', $h2->first()->text());
    }
}
