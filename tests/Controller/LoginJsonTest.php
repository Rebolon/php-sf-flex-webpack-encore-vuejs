<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests\Controller;

use App\Tests\Common\ToolsAbstract;

/**
 * Quick test on all login pages
 */
class LoginJsonTest extends ToolsAbstract
{
    /**
     * @group git-pre-push
     */
    public function testLogin()
    {
        $client = $this->getClient();
        $router = $this->getRouter();
        $uriSecured = $router->generate('demo_secured_page_json', []);
        $uriLogin = $router->generate('demo_login_json_check', []);
        $uriToken = $router->generate('token', []);
        $errMsg = sprintf("route: %s", $uriSecured);
        $headers = [
            'ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            ];

        $client->request('GET', $uriSecured);
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), $errMsg);
        $this->assertEquals(["error" => "Authentication Required", ], json_decode($client->getResponse()->getContent(), true), $errMsg);

        $client->request('POST', $uriLogin, [], [], $headers, json_encode(['login_username' => 15, 'login_password' => $this->testPwd,]));
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), $errMsg);
        $this->assertEquals(["error" => "_csrf_token mandatory", "code" => 420, ], json_decode($client->getResponse()->getContent(), true), $errMsg);

        $client->request('GET', $uriToken);
        $tokenRaw = $client->getResponse()->getContent();
        $token = json_decode($tokenRaw);

        $client->request('POST', $uriLogin, [], [], $headers, json_encode(['login_username' => 15, 'login_password' => $this->testPwd, '_csrf_token' => 'toto',]));
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), $errMsg);
        $this->assertEquals(["error" => "Invalid CSRF token.", "code" => 423, ], json_decode($client->getResponse()->getContent(), true), $errMsg);

        $client->request('POST', $uriLogin, [], [], $headers, json_encode(['login_username' => 15, 'login_password' => $this->testPwd, '_csrf_token' => $token,]));
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), $errMsg);
        $this->assertEquals(["error" => "Forbidden", "code" => 403, ], json_decode($client->getResponse()->getContent(), true), $errMsg);

        $client->request('POST', $uriLogin, [], [], $headers, json_encode(['login_username' => $this->testLogin, 'login_password' => $this->testPwd, '_csrf_token' => $token,]));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $errMsg);
        $result =json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('isLoggedIn', $result, $errMsg);
        $this->assertEquals(1, $result['isLoggedIn'], $errMsg);

        $crawler = $client->request('GET', $uriSecured);
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
