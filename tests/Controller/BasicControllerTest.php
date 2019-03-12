<?php

namespace App\Tests\Controller;

use App\Tests\Common\PantherToolsAbstract;

class BasicControllerTest extends PantherToolsAbstract
{
    /**
     * @var string
     */
    protected $content = '<html xmlns="http://www.w3.org/1999/xhtml"><head></head><body>This is the index action !</body></html>';

    /**
     * @group git-pre-push
     */
    public function testBasicController()
    {
        $client = static::createPantherClient(); // Your app is automatically started using the built-in web server
        $crawler = $client->request('GET', $this->getRouter()->generate('simple'));

        // because the BasicController only render text, we cannot use $crawler that expect a valid DOM. But we can use the client->getPageSource, except that it will embeed the text in an html>body node
        $this->assertContains($this->content, $client->getPageSource());
    }

    /**
     * @group git-pre-push
     */
    public function testBasicControllerFromHome()
    {
        $client = static::createPantherClient(); // Your app is automatically started using the built-in web server
        $crawler = $client->request('GET', $this->getRouter()->generate('index'));

        $link = $crawler->filter('ul li.list-group-item a');
        $client->click($link->first()->link());

        // because the BasicController only render text, we cannot use $crawler that expect a valid DOM. But we can use the client->getPageSource, except that it will embeed the text in an html>body node
        $this->assertContains($this->content, $client->getPageSource());
    }
}
