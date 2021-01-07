<?php

namespace App\Tests\Repository;

use App\Entity\Library\Book;
use App\Entity\Library\Loan;
use App\Entity\Library\Reader;
use App\Tests\Common\TestCase;
use App\Tests\Common\ToolsAbstract;
use App\Tests\Common\WebPagesAbstract;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReaderRepositoryTest extends WebPagesAbstract
{
    public function testWhoBorrowBook()
    {
        $em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->markTestIncomplete();
    }

    public function testWhoLoanBook()
    {
        $em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->markTestIncomplete();
    }
}
