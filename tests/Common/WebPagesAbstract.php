<?php

namespace App\Tests\Common;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DomCrawler\Crawler;

abstract class WebPagesAbstract extends ToolsAbstract
{
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
}
