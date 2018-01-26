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
    public function testLogin()
    {
        $client = $this->getClient();
        $router = $this->getRouter();
        $uri = $router->generate('demo_login_standard', []);

        $this->checkLogin($client, $uri);
    }

    /**
     * @group git-pre-push
     */
    public function testPages()
    {
        $this->markTestIncomplete('Should test some all pages');

        $this->displayTestSection();

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
        $client = $this->getClient();
        $router = $this->getRouter();

        $this->checkApi($router, $client);
    }
}
