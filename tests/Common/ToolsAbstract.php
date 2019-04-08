<?php

namespace App\Tests\Common;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\RouterInterface;

abstract class ToolsAbstract extends WebTestCase
{
    use TestCase;

    /**
     * @param array $options
     * @param array $server
     * @return mixed|Client
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

        $client = parent::createClient($options, $server);

        return $client;
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
     * @return Client;
     */
    protected function getClient()
    {
        // is it a good idea to use the same client on
        if (!$this->client) {
            $this->client = static::createClient();
            $this->client->followRedirects(true);
        }

        $this->client->restart();

        return $this->client;
    }

    /**
     * @param Client $client
     * @return Crawler
     */
    protected function doLoginStandard(Client $client)
    {
        $uri = $this->router->generate('demo_login_standard', [], Router::NETWORK_PATH);
        $crawler = $client->request('GET', $uri);
        $user = $this->profiles[$this->currentProfileIdx];
        $buttonCrawlerNode = $crawler->selectButton('login');
        $form = $buttonCrawlerNode->form([
            $client->getKernel()->getContainer()->getParameter('login_username_path') => $user['login'],
            $client->getKernel()->getContainer()->getParameter('login_password_path') => $user['pwd'],
        ]);
        $crawler = $client->submit($form);

        return $crawler;
    }
}
