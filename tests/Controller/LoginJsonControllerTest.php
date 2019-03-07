<?php

namespace App\Tests\Controller;

use App\Tests\Common\PantherToolsAbstract;

class LoginJsonControllerTest extends PantherToolsAbstract
{
    /**
     * @group git-pre-push
     */
    public function testLoginFail()
    {
        $client = static::createPantherClient(); // Your app is automatically started using the built-in web server
        $uri = $this->getRouter()->generate('app_formquasarvuejs_index');
        $crawler = $client->request('GET', $uri);

        $this->assertContains('Welcome to', $crawler->filter('h5')->text()); // You can use any PHPUnit assertion

        // for instance method form with array of value fails because Quasar QInput add 2 elements with the same attribute name
        // and Browserkit require that form field input is lonely input with same name in all DOM
        // so i can't test this for instance
        $inputUserName = $crawler->filter('form.login input[name=username]')->getElement(0);
        $inputUserName->clear();
        $inputUserName->sendKeys('fakeUser');

        $inputPwd = $crawler->filter('form.login input[name=password]')->getElement(0);
        $inputPwd->clear();
        $inputPwd->sendKeys('fakeUser11111');

        $form = $crawler->selectButton('LOGIN')->form();
        $crawler = $client->submit($form);
        $client->waitFor('div.q-alert.bg-warning');
        $this->assertContains($uri, $client->getCurrentURL());

        /* clear doesnot empty the input, so it just add string with send_keys and the test fail, so i split the test in 2 methods, i dislike this but no choice for instance
        $inputUserName->clear();
        $inputUserName->sendKeys('test_js');

        $inputPwd->clear();
        $inputPwd->sendKeys('test');

        $client->submit($form);
        $client->waitFor('div.q-alert.bg-info');
        $this->assertEquals('List of books', $crawler->filter('h5')->first()->text());
        */
    }

    /**
     * @group git-pre-push
     */
    public function testLoginSuccess()
    {
        //$this->markTestIncomplete('at the end of the test i dont know why but the crawler doesnot find the title `List of books`, instead it keeps the first title `Welcome to` ');

        $client = static::createPantherClient(); // Your app is automatically started using the built-in web server
        $uri = $this->getRouter()->generate('app_formquasarvuejs_index');
        $crawler = $client->request('GET', $uri);

        $this->assertContains('Welcome to', $crawler->filter('h5')->text()); // You can use any PHPUnit assertion

        $inputUserName = $crawler->filter('form.login input[name=username]')->getElement(0);
        $inputUserName->clear();
        $inputUserName->sendKeys('test_js');

        $inputPwd = $crawler->filter('form.login input[name=password]')->getElement(0);
        $inputPwd->clear();
        $inputPwd->sendKeys('test');

        $form = $crawler->selectButton('LOGIN')->form();
        $crawler = $client->submit($form);

        $client->waitFor('div.books h5.title');
        $this->assertEquals('List of books', $crawler->filter('h5')->text());
    }
}
