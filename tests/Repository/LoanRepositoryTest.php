<?php

namespace App\Tests\Repository;

use App\Entity\Library\Book;
use App\Entity\Library\Loan;
use App\Entity\Library\Reader;
use App\Tests\Common\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoanRepositoryTest extends KernelTestCase
{
    use TestCase;

    public function testIsAvailable()
    {
        $book = $this->em->find(Book::class, 4);
        $loaner = $this->em->find(Reader::class, 1);

        $rep = $this->em->getRepository(Loan::class);
        $isAvailable = $rep->isBookAvailable($book, $loaner);

        $this->assertFalse($isAvailable);

        $book = $this->em->find(Book::class, 3);
        $loaner = $this->em->find(Reader::class, 2);

        $rep = $this->em->getRepository(Loan::class);
        $isAvailable = $rep->isBookAvailable($book, $loaner);

        $this->assertTrue($isAvailable);
    }
}
