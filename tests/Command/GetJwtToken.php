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
    /**
     * @group git-pre-push
     */
    public function testGetJwtToken()
    {
        $this->markTestIncomplete('Should test command');
    }
}
