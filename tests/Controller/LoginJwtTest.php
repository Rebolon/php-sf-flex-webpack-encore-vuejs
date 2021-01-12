<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests\Controller;

use App\Tests\Common\WebPagesAbstract;

/**
 * Quick test on all login pages
 */
class LoginJwtTest extends WebPagesAbstract
{
    /**
     * @group git-pre-push
     */
    public function testLogin()
    {
        $client = $this->client;
        $router = $this->getRouter();
        $uriSecured = $router->generate('demo_secured_page_jwt', []);
        $uriLogin = $router->generate('api_login_check', []);
        $errMsg = sprintf("route: %s", $uriSecured);
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            ];
        $cbTrim = function ($item): string {
            return trim($item);
        };

        $client->request('GET', $uriSecured);
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), $errMsg);
        $this->assertEquals([
            'code' => 401,
            'message' => 'JWT Token not found',
        ], json_decode($client->getResponse()->getContent(), true), $errMsg);

        $client->request('POST', $uriLogin, [], [], $headers, json_encode(['login_username' => '15', 'login_password' => 'nopwd', ]));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), $errMsg);

        $user = $this->profiles[$this->currentProfileIdx];

        $client->request('POST', $uriLogin, [], [], $headers, json_encode(['login_username' => $user['login'], 'login_password' => $user['pwd'], ]));
        $tokenRaw = $client->getResponse()->getContent();
        $token = json_decode($tokenRaw);
        $headers['HTTP_AUTHORIZATION'] = 'Bearer ' . $token->token;

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);
        $this->assertObjectHasAttribute('token', $token, $errMsg);
        $this->assertNotEmpty($token->token, $errMsg);
        // @todo maybe test the validity of the token ?

        $crawler = $client->request('GET', $uriSecured, [], [], $headers);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);
        $text = implode(' ', array_map($cbTrim, explode("\n", trim($crawler->filter('body div.container')->text()))));
        $this->assertEquals('Hello Test_js You are in', $text, $errMsg);
    }
}
