<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={"access_control"="is_granted('ROLE_USER')"}
 * )
 *
 * @ORM\Entity
 * @ORM\Table()
 */
class Loan implements LibraryInterface
{
    /**
     * @Groups("user_read", "loan_read)
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Book",
     *     inversedBy="authors",
     *     fetch="EAGER",
     *     cascade={"remove"}
     * )
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     */
    private $book;

    /**
     * @ApiSubresource(maxDepth=1)
     * @Groups({"user_read", "loan_read", "loan_write"})
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Reader"
     *     fetch="EAGER"
     * )
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     */
    private $owner;

    /**
     * @ApiSubresource(maxDepth=1)
     * @Groups({"user_read", "loan_read", "loan_write"})
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Reader"
     *     fetch="EAGER"
     * )
     * @ORM\JoinColumn(name="loaner_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     */
    private $loaner;

    /**
     * @ApiSubresource()
     * @Groups({"user_read", "loan_read", "loan_write"})
     *
     * @Assert\DateTime()
     */
    private $startLoan;

    /**
     * @ApiSubresource()
     * @Groups({"user_read", "loan_read", "loan_write"})
     *
     * @Assert\DateTime()
     * @Assert\Blank()
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

    /**
     * Mandatory for EasyAdminBundle to build the select box
     * It also helps to build a footprint of the object, even if with the Serializer component it might be more pertinent
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getOwner()->__toString() . ' loan ' . $this->getBook()->getTitle() . ' to '
            . $this->getLoaner()->__toString() . ' at ' . $this->getStartLoan()->format('d/m/Y')
            . ($this->getEndLoan() ? ' and has been returned on ' . $this->getStartLoan()->format('d/m/Y')
                : ' and has not been returned');
    }
}
