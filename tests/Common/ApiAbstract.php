<?php

namespace App\Tests\Common;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DomCrawler\Crawler;
use PHPUnit\Util\Printer;

abstract class ApiAbstract extends ToolsAbstract
{
    /**
     * @return Crawler
     */
    protected function doLogin(Client $client)
    {
        $uri = $this->router->generate('demo_login_standard', [], Router::NETWORK_PATH);
        $crawler = $client->request('GET', $uri);
        $buttonCrawlerNode = $crawler->selectButton('login');
        $form = $buttonCrawlerNode->form(array(
            'login_username' => $this->testLogin,
            'login_password' => $this->testPwd,
        ));
        $crawler = $client->submit($form);

        return $crawler;
    }

    /**
     * @param $router
     *
     * @return array
     */
    protected function getApiRoutes(Router $router): array
    {
        $routesName = [];

        // @todo list manually routes to test and then generate them with valid parameters
        // following code is wrong coz it lacks valid params to generate routes
        foreach ($router->getRouteCollection() as $name => $route) {
            if (0 !== strpos($name,'api')) {
                continue;
            }

            $routesName[] = $router->generate($name, [], Router::NETWORK_PATH);
        }

        return $routesName;
    }
}
