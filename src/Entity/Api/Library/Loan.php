<?php
namespace App\Entity\Api\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Entity\Library\LibraryInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     attributes={"access_control"="is_granted('ROLE_USER')"}
 * )
 */
class Loan implements LibraryInterface
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"user_read", "loan_read"})
     */
    private $id;

    /**
     * @ApiSubresource(maxDepth=1)
     * @Groups({"user_read", "loan_read", "loan_write"})
     */
    private $book;

    /**
     * @ApiSubresource(maxDepth=1)
     * @Groups({"user_read", "loan_read", "loan_write"})
     */
    private $owner;

    /**
     * @ApiSubresource(maxDepth=1)
     * @Groups({"user_read", "loan_read", "loan_write"})
     */
    private $loaner;

    /**
     * @ApiSubresource()
     * @Groups({"user_read", "loan_read", "loan_write"})
     */
    private $startLoan;

    /**
     * @ApiSubresource()
     * @Groups({"user_read", "loan_read", "loan_write"})
     */
    private $endLoan;

    /**
     * mandatory for api-platform to get a valid IRI
     *
     * @return int
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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setLoaner(Reader $reader): self
    {
        $this->loaner = $reader;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartLoan(): ?\DateTime
    {
        return $this->startLoan;
    }

    /**
     * @param \DateTime $startLoan
     * @return self
     */
    public function setStartLoan(\DateTime $startLoan): self
    {
        $this->startLoan = $startLoan;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndLoan(): ?\DateTime
    {
        return $this->endLoan;
    }

    /**
     * @param \DateTime $endLoan
     * @return Loan
     */
    public function setEndLoan(\DateTime $endLoan): self
    {
        $this->endLoan = $endLoan;

        return $this;
    }
}
