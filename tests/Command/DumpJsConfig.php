<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests\Command;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 *
 */
class DumpJsConfig extends WebTestCase
{
    protected function setUp()
    {
        $this->router = $this->createMock('\Symfony\Component\Routing\RouterInterface');
        $this->twig = $this->createMock('\Twig_Environment');
    }

    /**
     * @group git-pre-push
     */
    public function testDumpJsConfigInfo()
    {
        $csrfTokenParameter = 'csrfToken';
        $apiPlatformPrefix = '/api';
        $loginUsernamePath = 'login';
        $loginPasswordPath = 'pwd';

        $tested = new \App\Command\DumpJsConfig($csrfTokenParameter, $apiPlatformPrefix, $loginUsernamePath, $loginPasswordPath, $this->twig, $this->router);
        $this->assertTrue($tested->isEnabled(), 'Should be enabled');
        $this->assertEquals($tested->getName(), 'app:dump-js-config');
        $this->assertEquals($tested->getDescription(), 'Create the config.js file.');
        $this->assertEquals($tested->getHelp(), 'Dump symfony configuration into a config.js file available from assets/js/*');
    }

    /**
     * @group git-pre-push
     */
    public function testDumpJsConfigRun()
    {
        $this->markTestIncomplete('Should test run command, but needs to change the filepath of the js file or it will be overwritten by test');
    }
}
