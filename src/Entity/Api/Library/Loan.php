<?php
namespace App\Entity\Api\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Entity\Library\Book;
use App\Entity\Library\LibraryInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use \DateTime;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ ApiResource(
 *     attributes={
 *          "access_control"="is_granted('ROLE_USER')",
 *          "normalization_context"={
 *              "groups"={"user_read", "loan_read"}
 *          },
 *          "denormalization_context"={
 *              "groups"={"user_write", "loan_write"}
 *          }
 *     }
 * )
 */
class Loan implements LibraryInterface
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"user_read", "loan_read"})
     * @var int
     */
    protected $id;

    /**
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @Groups({"user_read", "loan_read", "loan_write"})
     * @var Book
     */
    protected $book;

    /**
     * The reader that borrow the books
     *
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @Groups({"user_read", "loan_read", "loan_write"})
     * @var Reader
     */
    protected $borrower;

    /**
     * The reader that loan the books (the owner in fact)
     *
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @Groups({"user_read", "loan_read", "loan_write"})
     * @var Reader
     */
    protected $loaner;

    /**
     * @Groups({"user_read", "loan_read", "loan_write"})
     * @var DateTime|null
     */
    protected $startLoan;

    /**
     * @Groups({"user_read", "loan_read", "loan_write"})
     * @var DateTime|null
     */
    protected $endLoan;

    /**
     * mandatory for api-platform to get a valid IRI
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Book
     */
    public function getBook(): Book
    {
        return $this->book;
    }

    /**
     * @param Book $book
     * @return self
     */
    public function setBook(Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    /**
     * @return Reader
     */
    public function getBorrower(): Reader
    {
        return $this->borrower;
    }

    /**
     * @param Reader $reader
     * @return self
     */
    public function setBorrower(Reader $reader): self
    {
        $this->borrower = $reader;

        return $this;
    }

    /**
     * @return Reader
     */
    public function getLoaner(): Reader
    {
        return $this->loaner;
    }

    /**
     * @param Reader $reader
     * @return self
     */
    public function setLoaner(Reader $reader): self
    {
        $this->loaner = $reader;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getStartLoan(): ?DateTime
    {
        return $this->startLoan;
    }

    /**
     * @param DateTime $startLoan
     * @return self
     */
    public function setStartLoan(DateTime $startLoan): self
    {
        $this->startLoan = $startLoan;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndLoan(): ?DateTime
    {
        return $this->endLoan;
    }

    /**
     * @param DateTime $endLoan
     * @return Loan
     */
    public function setEndLoan(DateTime $endLoan): self
    {
        $this->endLoan = $endLoan;

        return $this;
    }
}
