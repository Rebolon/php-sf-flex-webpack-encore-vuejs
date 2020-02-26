<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests\Command;
use App\Command\DumpJsConfig;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 *
 */
class DumpJsConfigTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Environment
     */
    protected $twig;

    protected function setUp(): void
    {
        $this->router = $this->createMock('\Symfony\Component\Routing\RouterInterface');
        $this->twig = $this->createMock('\Twig\Environment');
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
        $tokenJwtBearer = 'Bearer';
        // a folder with a config folder
        $kernelProjectDir = __DIR__ . '/../fixtures/';

        $tested = new DumpJsConfig($csrfTokenParameter, $apiPlatformPrefix, $loginUsernamePath, $loginPasswordPath, $tokenJwtBearer, $kernelProjectDir, $this->twig, $this->router);
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
