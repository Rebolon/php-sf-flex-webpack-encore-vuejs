<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 *
 */
class CommandTest extends WebTestCase
{
    /**
     * @group git-pre-push
     */
    public function testDumpJsConfig()
    {
        $this->markTestIncomplete('Should test command');
    }
}
