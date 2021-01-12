<?php

namespace App\Tests\Common;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class WebPagesAbstract  extends WebTestCase
{
    use TestCase;

    /**
     * @param KernelBrowser $client
     * @return Crawler
     */
    protected function doLogin(KernelBrowser $client)
    {
        $uri = $this->router->generate('demo_login_standard', [], Router::NETWORK_PATH);
        $crawler = $client->request('GET', $uri);
        $user = $this->profiles[$this->currentProfileIdx];
        $buttonCrawlerNode = $crawler->selectButton('login');
        $form = $buttonCrawlerNode->form([
            'login_username' => $user['login'],
            'login_password' => $user['pwd'],
        ]);
        $crawler = $client->submit($form);

        return $crawler;
    }

    /**
     * Test SEO basic fields
     *
     * @param Crawler $crawler
     * @param string $errMsg
     * @return mixed
     */
    protected function checkSEO(Crawler $crawler, $errMsg)
    {
        $filter = $crawler->filter('head title');
        $this->assertCount(1, $filter, $errMsg);
        $this->assertNotEmpty($filter->text(), $errMsg);

        return $crawler;
    }

    /**
     * Test SEO basic fields
     *
     * @param Crawler $crawler
     * @param string $errMsg
     * @return mixed
     */
    protected function checkHeader(Crawler $crawler, $errMsg)
    {
        $filter = $crawler->filter('body nav h1');
        $this->assertCount(1, $filter, $errMsg);
        $expected = '<a href="/"><u>Demo of symfony 5</u></a>
                    <span style="font-size: smaller">with flex,
                        <span style="font-size: smaller">and webpack/encore,
                            <span style="font-size: smaller">VueJS,
                                <span style="font-size: smaller">ApiPlatform,
                                    <span style="font-size: smaller">HttpClient,...</span>
                                </span>
                            </span>
                        </span>
                    </span>
';
        $this->assertEquals(
            trim(strtr($expected, ["\r\n" => "\n", ])),
            trim(strtr($filter->html(), ["\r\n" => "\n", ])),
            $errMsg
        );

        return $crawler;
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
}
