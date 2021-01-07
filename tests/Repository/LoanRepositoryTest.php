<?php

namespace App\Tests\Repository;

use App\Entity\Library\Book;
use App\Entity\Library\Loan;
use App\Entity\Library\Reader;
use App\Tests\Common\ToolsAbstract;
use App\Tests\Common\WebPagesAbstract;

class LoanRepositoryTest extends WebPagesAbstract
{
    public function testIsAvailable()
    {
        $em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $book = $em->find(Book::class, 4);
        $loaner = $em->find(Reader::class, 1);

        $rep = $em->getRepository(Loan::class);
        $isAvailable = $rep->isBookAvailable($book, $loaner);

        $this->assertFalse($isAvailable);

        $book = $em->find(Book::class, 3);
        $loaner = $em->find(Reader::class, 2);

        $rep = $em->getRepository(Loan::class);
        $isAvailable = $rep->isBookAvailable($book, $loaner);

        $this->assertTrue($isAvailable);
    }
}
