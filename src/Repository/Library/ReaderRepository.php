<?php

namespace App\Repository\Library;

use App\Entity\Library\Book;
use App\Entity\Library\Reader;
use Doctrine\ORM\EntityRepository;

/**
 * BookRepository
 */
class ReaderRepository extends EntityRepository
{
    /**
     * @param Book $book
     * @param Reader $loaner
     * @return bool
     */
    public function whoBorrowBook(Book $book, Reader $loaner)
    {
        $params = [
            1 => $book->getId(),
            2 => $loaner->getId(),
        ];

        $qb = $this->createQueryBuilder('r');
        $qb->select('r.*')
            ->innerJoin('r.loans', 'l')
            ->where('l.book = ?1')
            ->andWhere('l.loaner = ?2')
            ->addOrderBy('l.startLoan', 'DESC')
            ->setParameters($params);

        $qry = $qb->getQuery();
        $res = $qry->getResult();

        return $res;
    }

    /**
     * @param Book $book
     * @param Reader $loaner
     * @return mixed
     */
    public function whoLoanBook(Book $book, Reader $loaner)
    {
        $params = [
            1 => $book->getId(),
            2 => $loaner->getId(),
        ];

        $qb = $this->createQueryBuilder('r');
        $qb->select('r.*')
            ->innerJoin('r.borrows', 'l')
            ->where('l.book = ?1')
            ->andWhere('l.borrower = ?2')
            ->addOrderBy('l.startLoan', 'DESC')
            ->setParameters($params);

        $qry = $qb->getQuery();
        $res = $qry->getResult();

        return $res;
    }
}
