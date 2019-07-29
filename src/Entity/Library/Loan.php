<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use \DateTime;

/**
 * @ApiResource(
 *     attributes={
 *          "access_control"="is_granted('ROLE_USER')",
 *          "pagination_client_enabled"=true,
 *          "normalization_context"={
 *              "groups"={"loan_read"}
 *          },
 *          "denormalization_context"={
 *              "groups"={"loan_write"}
 *          }
 *     }
 * )
 *
 * @ApiFilter(OrderFilter::class, properties={"id", "title"})
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "title": "istart", "description": "partial", "tags.name"="exact"})
 * @ApiFilter(PropertyFilter::class, arguments={"parameterName": "properties", "overrideDefaultProperties": false}))
 *
 * @ORM\Entity(repositoryClass="App\Repository\Library\LoanRepository")
 * @ORM\Table()
 */
class Loan implements LibraryInterface
{
    /**
     * @ApiProperty(identifier=true)
     *
     * @Groups({"loan_read"})
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Assert\Uuid()
     *
     * @var int
     */
    protected $id;

    /**
     * @ApiSubresource(maxDepth=1)
     *
     * @Groups({"loan_read"})
     * @MaxDepth(1)
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Book",
     *     inversedBy="loans",
     *     fetch="EAGER",
     *     cascade={"remove"}
     * )
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     *
     * @var Book
     */
    protected $book;

    /**
     * The reader that borrow the books
     *
     * @ApiSubresource(maxDepth=1)
     *
     * @Groups({"loan_read", "loan_write"})
     * @MaxDepth(1)
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Reader",
     *     inversedBy="borrows",
     *     fetch="EAGER"
     * )
     * @ORM\JoinColumn(name="borrower_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     *
     * @var Reader
     */
    protected $borrower;

    /**
     * The reader that loan the books (the owner in fact)
     *
     * @ApiSubresource(maxDepth=1)
     *
     * @Groups({"loan_read", "loan_write"})
     * @MaxDepth(1)
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Reader",
     *     inversedBy="loans",
     *     fetch="EAGER"
     * )
     * @ORM\JoinColumn(name="loaner_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     *
     * @var Reader
     */
    protected $loaner;

    /**
     * @Groups({"loan_read", "loan_write"})
     *
     * @ORM\Column(type="datetime", nullable=false, name="start_loan")
     *
     * @Assert\DateTime()
     * @Assert\NotBlank()
     *
     * @var DateTime
     */
    protected $startLoan;

    /**
     * @Groups({"loan_read", "loan_write"})
     *
     * @ORM\Column(type="datetime", nullable=true, name="end_loan")
     *
     * @Assert\DateTime()
     * @Assert\Blank()
     *
     * @var DateTime|null
     */
    protected $endLoan;

    /**
     * Loan constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        // default value coz since mid-2018 (don't have exact DBAL version) there is no more default attributes for datetime
        $this->setStartLoan(new DateTime());
    }

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

    /**
     * Mandatory for EasyAdminBundle to build the select box
     * It also helps to build a footprint of the object, even if with the Serializer component it might be more pertinent
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getBorrower()->__toString() . ' borrow ' . $this->getBook()->getTitle() . ' from '
            . $this->getLoaner()->__toString() . ' at ' . $this->getStartLoan()->format('d/m/Y')
            . ($this->getEndLoan() ? ' and has been returned on ' . $this->getStartLoan()->format('d/m/Y')
                : ' and has not been returned yet');
    }
}
