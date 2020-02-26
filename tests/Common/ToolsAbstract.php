<?php

namespace App\Tests\Common;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class ToolsAbstract extends WebTestCase
{
    use TestCase;

    /**
     * @param array $options
     * @param array $server
     * @return mixed|KernelBrowser
     */
    protected static function createClient(array $options = [], array $server = [])
    {
        $env = getenv();

        $options = array_merge(['debug' => false], $options);

        // when launched by npm
        if (array_key_exists('npm_package_config_server_host_web', $env)) {
            $server = array_merge([
                'HTTP_HOST' => $env['npm_package_config_server_host_web'] . ':' . $env['npm_package_config_server_port_web'],
            ], $server);
        }

        return parent::createClient($options, $server);
    }

    /**
     * Because WebTestCase require HTTP headers to be prefixed with HTTP_
     * This methods will do it for you, for specified headers
     *
     * @param array $headers
     * @return array
     */
    protected function prepareHeaders($headers = [])
    {
        foreach ($headers as $keys => $value) {
            $prefix = 'HTTP_';
            if (strpos($keys, $prefix) === 0) {
                continue;
            }

            $headers[$prefix . $keys] = $value;
            unset($headers[$keys]);
        }

        return $headers;
    }

    /**
     * @param KernelBrowser $client
     * @return Crawler
     */
    protected function doLoginStandard(KernelBrowser $client)
    {
        $uri = $this->router->generate('demo_login_standard', [], Router::NETWORK_PATH);
        $crawler = $client->request('GET', $uri);
        $user = $this->profiles[$this->currentProfileIdx];
        $buttonCrawlerNode = $crawler->selectButton('login');
        $form = $buttonCrawlerNode->form([
            static::$container->getParameter('login_username_path') => $user['login'],
            static::$container->getParameter('login_password_path') => $user['pwd'],
        ]);
        $crawler = $client->submit($form);

        return $crawler;
    }
}
