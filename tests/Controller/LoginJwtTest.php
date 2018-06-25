<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests\Controller;

use App\Tests\Common\ToolsAbstract;

/**
 * Quick test on all login pages
 */
class LoginJwtTest extends ToolsAbstract
{
    /**
     * @group git-pre-push
     */
    public function testLogin()
    {
        $client = $this->getClient();
        $router = $this->getRouter();
        $uriSecured = $router->generate('demo_secured_page_jwt', []);
        $uriLogin = $router->generate('app_loginjwt_newtoken', []);
        $errMsg = sprintf("route: %s", $uriSecured);
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            ];

        $client->request('GET', $uriSecured);
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), $errMsg);
        $this->assertEquals(["error" => "Authentication Required", ], json_decode($client->getResponse()->getContent(), true), $errMsg);

        $client->request('POST', $uriLogin, [], [], $headers, json_encode(['login_username' => 15, 'login_password' => $this->testPwd, ]));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), $errMsg);
        // should not be 403 + Forbidden ? when credentials are wrong
        // @todo why i don't have a JSON response ? CONTENT-TYPE is application/json so ?
        // $this->assertEquals(["error" => "Notfound", ], json_decode($client->getResponse()->getContent(), true), $errMsg);

        $client->request('POST', $uriLogin, [], [], $headers, json_encode(['login_username' => $this->testLogin, 'login_password' => $this->testPwd, ]));
        $tokenRaw = $client->getResponse()->getContent();
        $token = json_decode($tokenRaw);
        $headers['HTTP_AUTHORIZATION'] = $token->token;

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);
        $this->assertObjectHasAttribute('token', $token, $errMsg);
        $this->assertNotEmpty($token->token, $errMsg);
        // @todo maybe test the validity of the token ?

        $crawler = $client->request('GET', $uriSecured, [], [], $headers);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);
        $text = implode(' ', array_map(function($item) {
            $trimmed = trim($item);
            if ($trimmed) {
                return $trimmed;
            }
        }, explode("\n", trim($crawler->filter('body div')->text()))));
        $this->assertEquals('Hello Test_js You are in', $text, $errMsg);
    }
}
