<?php

namespace App\Tests\Common;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DomCrawler\Crawler;

abstract class WebPagesAbstract extends ToolsAbstract
{
    /**
     * @return Crawler
     */
    protected function doLogin(Client $client)
    {
        $uri = $this->router->generate('demo_login_standard', [], Router::NETWORK_PATH);
        $crawler = $client->request('GET', $uri);
        $buttonCrawlerNode = $crawler->selectButton('login');
        $form = $buttonCrawlerNode->form([
            'login_username' => $this->testLogin,
            'login_password' => $this->testPwd,
        ]);
        $crawler = $client->submit($form);

        return $crawler;
    }

    /**
     * Test SEO basic fields
     *
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
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
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param string $errMsg
     * @return mixed
     */
    protected function checkHeader(Crawler $crawler, $errMsg)
    {
        $filter = $crawler->filter('body nav h1');
        $this->assertCount(1, $filter, $errMsg);
        $expected = '<a href="/"><u>Demo of symfony 4</u></a>
                <span style="font-size: smaller">with flex,
                    <span style="font-size: smaller">and webpack/encore,
                        <span style="font-size: smaller">VueJS,
                            <span style="font-size: smaller">ApiPlatform,
                                <span style="font-size: smaller">HttpPlug,...</span>
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
