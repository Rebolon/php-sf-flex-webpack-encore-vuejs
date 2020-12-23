<?php

namespace App\Tests\Controller;

use App\Tests\Common\PantherToolsAbstract;

class LoginPhpControllerTest extends PantherToolsAbstract
{
    public function setUp(): void
    {
        parent::setUp();

        $this->currentProfileIdx = 1;
    }

    /**
     * @group git-pre-push
     */
    public function testLoginFailAndSucceed()
    {
        $client = static::createPantherClient(); // Your app is automatically started using the built-in web server
        $uri = $this->getRouter()->generate('demo_login_standard');
        $crawler = $client->request('GET', $uri);

        $this->assertCount(1, $crawler->filter('body')); // if i test on h5 it fails so for instance i do test on body... crappy
        $this->assertStringContainsString('Welcome to', $crawler->filter('body')->text());

        // for instance method form with array of value fails because Quasar QInput add 2 elements with the same attribute name
        // and Browserkit require that form field input is lonely input with same name in all DOM
        // so i can't test this for instance
        $inputUserName = $crawler->filter('form #username')->getElement(0);
        $inputUserName->clear();
        $inputUserName->sendKeys('fakeUser');

        $inputPwd = $crawler->filter('form #password')->getElement(0);
        $inputPwd->clear();
        $inputPwd->sendKeys('fakeUser11111');

        $form = $crawler->selectButton('login')->form();
        $crawler = $client->submit($form);
        $client->waitFor('div.alert.alert-danger');
        $this->assertEquals('Invalid credentials.', $crawler->filter('div.alert.alert-danger')->text());
        $this->assertStringContainsString($uri, $client->getCurrentURL());

        $user = $this->profiles[$this->currentProfileIdx];
        $inputUserName = $crawler->filter('form #username')->getElement(0);
        $inputUserName->clear();
        $inputUserName->sendKeys($user['login']);

        $inputPwd = $crawler->filter('form #password')->getElement(0);
        $inputPwd->clear();
        $inputPwd->sendKeys($user['pwd']);

        $form = $crawler->selectButton('login')->form();
        $crawler = $client->submit($form);

        $hello = $crawler->filter('.container')->getElement(0)->getText();
        $this->assertStringContainsString('Hello Test_php', $hello);
    }
}
