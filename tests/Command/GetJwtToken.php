<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests\Command;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 *
 */
class GetJwtToken extends WebTestCase
{
    protected function setUp()
    {
        $this->logger = $this->createMock('\Psr\Log\LoggerInterface');
        $this->provider = $this->createMock('\Symfony\Component\Security\Core\User\InMemoryUserProvider');
        $this->encoder = $this->createMock('\Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface');
        $this->passwordEncoder = $this->createMock('\Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface');
        $this->tokenTools = $this->createMock('\App\Security\JwtTokenTools');
    }

    /**
     * @group git-pre-push
     */
    public function testDumpJsConfigInfo()
    {
        $tested = new \App\Command\GetJwtToken($this->logger, $this->provider, $this->encoder, $this->passwordEncoder, $this->tokenTools);
        $this->assertTrue($tested->isEnabled(), 'Should be enabled');
        $this->assertEquals($tested->getName(), 'app:new-jwt-token');
        $this->assertEquals($tested->getDescription(), 'Generate a new valid JWT token');
        $this->assertEquals($tested->getHelp(), 'Generate a new valid JWT token, to use with API for example.');
    }

    /**
     * @group git-pre-push
     */
    public function testDumpJsConfigRun()
    {
        $this->markTestIncomplete('Should test run command');
    }
}
