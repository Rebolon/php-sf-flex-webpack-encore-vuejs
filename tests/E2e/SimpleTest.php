<?php

namespace App\Tests\E2e;

use Symfony\Component\Panther\PantherTestCase;

class SimpleTest extends PantherTestCase
{
    /**
     * group fix
     */
    public function testBasicController()
    {
        $client = static::createPantherClient(); // Your app is automatically started using the built-in web server
        $crawler = $client->request('GET', '/');

        $this->assertContains('Demo of symfony 4 with flex, and webpack/encore, VueJS, ApiPlatform, HttpPlug,...', $crawler->filter('nav ha1')->text()); // You can use any PHPUnit assertion

        $link = $crawler->filter('ul li.list-group-item a')->first();
        $client->click($link);

        $this->assertContains('This is the index action !', $crawler->text());
    }
}
