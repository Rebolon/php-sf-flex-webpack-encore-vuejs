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
 *     security="is_granted('ROLE_USER')",
 *     paginationClientEnabled=true,
 *     normalizationContext={
 *         "groups"={"loan:read"}
 *     },
 *     denormalizationContext={
 *         "groups"={"loan:write"}
 *     },
 *     forceEager=false
 * )
 *
 * @ApiFilter(OrderFilter::class, properties={"id", "title"})
 * @ApiFilter(SearchFilter::class, properties={"title": "istart", "description": "partial", "tags.name"="exact"})
 * @ApiFilter(PropertyFilter::class, arguments={"parameterName": "properties", "overrideDefaultProperties": false}))
 *
 * @ORM\Entity(repositoryClass="App\Repository\Library\LoanRepository")
 * @ORM\Table(name="loan")
 */
class Loan implements LibraryInterface
{
    /**
     * @ApiProperty(identifier=true)
     *
     * @Groups({"loan:read"})
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var ?int
     */
    protected ?int $id = null;

    /**
     * @ApiSubresource(maxDepth=1)
     *
     * @Groups({"loan:read"})
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
    protected Book $book;

    /**
     * The reader that borrow the books
     *
     * // disable subResource coz it fails for instance
     * @ ApiSubresource(maxDepth=1)
     *
     * @Groups({"loan:read", "loan:write"})
     * @MaxDepth(1)
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Reader",
     *     inversedBy="borrows",
     *     fetch="EAGER",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="borrower_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     *
     * @var Reader
     */
    protected Reader $borrower;

    /**
     * The reader that loan the books (the owner in fact)
     *
     * // disable subResource coz it fails for instance
     * @ ApiSubresource(maxDepth=1)
     *
     * @Groups({"loan:read", "loan:write"})
     * @MaxDepth(1)
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Reader",
     *     inversedBy="loans",
     *     fetch="EAGER",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="loaner_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     *
     * @var Reader
     */
    protected Reader $loaner;

    /**
     * @Groups({"loan:read", "loan:write"})
     *
     * @ORM\Column(type="datetime", nullable=false, name="start_loan")
     *
     * @Assert\DateTime()
     * @Assert\NotBlank()
     *
     * @var DateTime
     */
    protected DateTime $startLoan;

    /**
     * @Groups({"loan:read", "loan:write"})
     *
     * @ORM\Column(type="datetime", nullable=true, name="end_loan")
     *
     * @Assert\DateTime()
     * @Assert\Blank()
     *
     * @var DateTime|null
     */
    protected ?DateTime $endLoan;

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
     * @param bool $updateRelation
     * @return $this
     */
    public function setBook(Book $book, bool $updateRelation = true): self
    {
        $this->book = $book;

        if ($updateRelation) {
            $this->book->addLoan($this, false);
        }

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
     * @param bool $updateRelation
     * @return self
     */
    public function setBorrower(Reader $reader, bool $updateRelation = true): self
    {
        $this->borrower = $reader;

        if ($updateRelation) {
            $this->borrower->addLoan($this, false);
        }

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
     * @param bool $updateRelation
     * @return self
     */
    public function setLoaner(Reader $reader, bool $updateRelation = true): self
    {
        $this->loaner = $reader;

        if ($updateRelation) {
            $this->loaner->addLoan($this, false);
        }

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
