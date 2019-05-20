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
 * @ApiResource(
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
    private $id;

    /**
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @Groups({"user_read", "loan_read", "loan_write"})
     * @var Book
     */
    private $book;

    /**
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @Groups({"user_read", "loan_read", "loan_write"})
     * @var Reader
     */
    private $owner;

    /**
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @Groups({"user_read", "loan_read", "loan_write"})
     * @var Reader
     */
    private $loaner;

    /**
     * @Groups({"user_read", "loan_read", "loan_write"})
     * @var DateTime|null
     */
    private $startLoan;

    /**
     * @Groups({"user_read", "loan_read", "loan_write"})
     * @var DateTime|null
     */
    private $endLoan;

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
    public function getOwner(): Reader
    {
        return $this->owner;
    }

    /**
     * @param Reader $reader
     * @return self
     */
    public function setOwner(Reader $reader): self
    {
        $this->owner = $reader;

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
