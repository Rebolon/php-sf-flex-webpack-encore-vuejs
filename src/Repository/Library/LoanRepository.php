<?php

namespace App\Repository\Library;

use App\Entity\Library\Book;
use App\Entity\Library\Reader;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * LoanRepository
 */
class LoanRepository extends EntityRepository
{
    /**
     * @param Book $book
     * @param Reader $loaner
     * @return bool
     */
    public function isBookAvailable(Book $book, Reader $loaner)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->where('l.book = ?1')
            ->andWhere('l.loaner = ?2')
            ->andWhere('l.endLoan IS NULL')
            ->addOrderBy('l.startLoan', 'DESC')
            ->setMaxResults(1)
            ->setParameters([
                1 => $book->getId(),
                2 => $loaner->getId(),
            ]);

        $qry = $qb->getQuery();
        $res = $qry->getResult(Query::HYDRATE_ARRAY);

        return (bool)!$res;
    }
}
