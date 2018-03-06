<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Quick test on all en page that should return at least 200 OK + some other checks
 *
 * Take care : those tests depends on DB for instance, and on parameters.yml (test_MYKEY)
 * We have to use fixtures or SQLITE dbs with required data to make the app run in test mode (or mock everything)
 */
class HTTP200Test extends HTTP200Abstract
{
    /**
     * @group git-pre-push
     */
    public function testParamConverter()
    {
        $this->markTestIncomplete('testParamConverter needs to be tested');

        $client = $this->getClient();
        $router = $this->getRouter();
        $uri = $router->generate('book_special_sample4', []);

        $errMsg = sprintf("route: %s", $uri);

        $body = <<<JSON
{
    "book": {
        "title": "test depuis special4",
        "editors": [{
            "publication_date": "1519664915", 
            "collection": "Hachette collection bis", 
            "isbn": "2-87764-257-7", 
            "editor": {
                "name": "JeanPaul Edition"
            }
        }, {
            "publication_date": "1519747464", 
            "collection": "Ma Tu vue", 
            "isbn": "2-87764-257-7", 
            "editor": {
                "name": "JeanPaul Edition"
            }
        }],
        "authors": [{
            "role": {
                "translation_key": "WRITER"
            }, 
            "author": {
                "firstname": "Marc", 
                "lastname": "Douche"
            }
        }, {
            "role": {
                "translation_key": "DRAWER"
            }, 
            "author": {
                "firstname": "Paul", 
                "lastname": "TRUC"
            }
        }],
        "serie": {
            "name": "ouaou ma serie"
        }
    }
}
JSON;

        $crawler = $client->request('POST', $uri, [], [], [], $body);

        // @todo test the json return
        $body = $crawler->filter('body');

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);

        $this->assertEquals(1, $body->count(), $errMsg);
        $this->assertNotEquals("", trim($body->text()), $errMsg);

        $this->checkSEO($crawler, $errMsg);
        $this->checkHeader($crawler, $errMsg);

        $form = $crawler->selectButton('login')->form();
        $form->setValues(array(
            'login_username' => $this->testLogin,
            'login_password' => 'fake',
        ));
        $crawler = $client->submit($form);
        $bc = $crawler->filter('body div.alert');
        $this->assertContains('Invalid credentials.', trim($bc->text()));

        $crawler = $this->doLogin($client);
        $bc = $crawler->filter('body div.container');
        $text = trim($bc->text());
        $this->assertContains('Hello Test', $text);
        $this->assertContains('You are in', $text);

        return $crawler;
    }

    /**
     * @group git-pre-push
     */
    public function testPages()
    {
        $client = $this->getClient();
        $router = $this->getRouter();

        $this->checkPages($router, $client);
    }

    /**
     * Is it really pertinent to test all api ? most of them are managed by ApiPlatform, so we should test only specific
     * api
     *
     * @group git-pre-push
     */
    public function testAPI()
    {
        $this->markTestIncomplete('checkApi is not ok coz it should test only custom routes');

        $client = $this->getClient();
        $router = $this->getRouter();

        $this->checkApi($router, $client);
    }
}
