<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AppAssert;

/**
 * @ApiResource(
 *     iri="http://bib.schema.org/user",
 *     paginationClientEnabled=true,
 *     normalizationContext={
 *         "groups"={"reader:read"}
 *     },
 *     denormalizationContext={
 *         "groups"={"reader:write"}
 *     },
 *     collectionOperations={
 *          "get",
 *          "post"={"security"="is_granted('ROLE_ADMIN')", "securityMessage"="Only admin users can add users, or the user himself for their own informations."}
 *     },
 *     itemOperations={
 *         "get",
 *         "put"={"security"="is_granted('ROLE_ADMIN')", "securityMessage"="Only admin users can modify users, or the user himself for their own informations."},
 *         "delete"={"security"="is_granted('ROLE_ADMIN')", "securityMessage"="Only admin users can delete users, or the user himself for his own informations."}
 *     }
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "lastname"})
 * @ApiFilter(SearchFilter::class, properties={"lastname": "istart", "firstname": "istart"})
 * @ApiFilter(PropertyFilter::class, arguments={"parameterName": "properties", "overrideDefaultProperties": false}))
 *
 * @ORM\Entity(repositoryClass="App\Repository\Library\ReaderRepository")
 *
 * @AppAssert\HasOneOfUserOrNameNotNull()
 */
class Reader implements LibraryInterface
{
    /**
     * @ApiProperty(
     *     identifier=true,
     *     iri="http://schema.org/identifier"
     * )
     *
     * @Groups({"reader:read"})
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
     * @ApiProperty(
     *     iri="http://schema.org/familyname"
     * )
     *
     * @Groups({"reader:read", "reader:write"})
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Length(max="255")
     *
     * @var string
     */
    protected $lastname;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/givenName"
     * )
     *
     * @Groups({"reader:read", "reader:write"})
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Length(max="255")
     *
     * @var string
     */
    protected $firstname;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/email"
     * )
     *
     * @Groups({"reader:read", "reader:write"})
     *
     * @ORM\Column(type="text", nullable=false)
     *
     * @Assert\Length(max="320")
     * @Assert\Email()
     *
     * @var string
     */
    protected $email;

    /**
     * @todo it may not be a list of books but a list of projectEdition coz you may get a book more than once but in
     * different edition ! For instance i keep this implementation for the sample but i might improve this in future
     *
     * @ApiSubresource(maxDepth=1)
     *
     * @Groups({"reader:read", "reader:write"})
     * @MaxDepth(1)
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Library\Book")
     * @ORM\JoinTable(name="reader_collection",
     *      joinColumns={
     *          @ORM\JoinColumn(name="reader_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     *      }
     * )
     *
     * @var Collection|Book[]
     */
    protected $books;

    /**
     * List of book a reader has borrowed to another reader
     *
     * @ApiSubresource(maxDepth=1)
     *
     * @Groups({"reader:read"})
     * @MaxDepth(1)
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\Loan", mappedBy="loaner")
     * @var Collection|Loan[]
     */
    protected $loans;

    /**
     * List of book a reader has borrowed from another reader
     *
     * @ApiSubresource(maxDepth=1)
     *
     * @Groups({"reader:read"})
     * @MaxDepth(1)
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\Loan", mappedBy="borrower")
     * @var Collection|Loan[]
     */
    protected $borrows;

    /**
     * Book constructor.
     */
    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->loans = new ArrayCollection();
        $this->borrows = new ArrayCollection();
    }

    /**
     * id can be null until flush is done
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
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return self
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     * @return self
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return self
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @param ArrayCollection $aLibrary
     * @return self
     */
    public function setBooks(ArrayCollection $aLibrary): self
    {
        $this->books = $aLibrary;

        return $this;
    }

    /**
     * @param Book $book
     * @return self
     */
    public function addBook(Book $book): self
    {
        if ($this->books->contains($book)
            || $this->hasBookInBooks($book)) {
            return $this;
        }

        $this->books->add($book);

        return $this;
    }

    /**
     * @todo move into a DTO, this is domain code
     *
     * @param Book $book
     * @return bool
     */
    protected function hasBookInBooks(Book $book): bool
    {
        // @todo check performance: it may be better to do a DQL to check instead of doctrine call to properties that may do new DB call
        // but in that case it must be moved into into repository and a DTO must controlle the workflow
        foreach ($this->books as $bookIAlreadyGet) {
            if (
                (
                    (!is_null($book->getId())
                    && $book->getId() === $bookIAlreadyGet->getId())
                ) || (
                    (is_null($book->getId())
                    && $book->getTitle() === $bookIAlreadyGet->getTitle())
                )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Loan[]|Collection
     */
    public function getLoans()
    {
        return $this->loans;
    }

    /**
     * @param Loan[]|Collection $loans
     * @return Reader
     */
    public function setLoans($loans): self
    {
        $this->loans = $loans;

        return $this;
    }

    /**
     * @todo a large part of the code below should be moved into DTO
     *
     * @param Loan $loan
     * @return $this
     */
    public function addLoan(Loan $loan)
    {
        if (!$loan->getLoaner()) {
            $loan->setLoaner($this);
        }

        if ($loan->getLoaner() !== $this) {
            throw new InvalidArgumentException('A loan can be added to reader s loan list only if he is the loaner in the Loan object');
        }

        if (!$loan->getBook()) {
            throw new InvalidArgumentException('A loan can be added to reader s loan list only if the book is in the Loan object');
        }

        if (!$this->books->contains($loan->getBook())) {
            throw new InvalidArgumentException('A loan can be added to reader s loan list only if the book exists in the reader s books collection');
        }

        if ($this->loans->contains($loan)) {
            return $this;
        }

        // @todo check if the book of the owner is available or already borrowed by someone: throw an exception to explain that it must be returned before it can be loaned again

        $this->loans->add($loan);

        return $this;
    }

    /**
     * @return Loan[]|Collection
     */
    public function getBorrows()
    {
        return $this->borrows;
    }

    /**
     * @param Loan[]|Collection $borrows
     * @return Reader
     */
    public function setBorrows($borrows): self
    {
        $this->borrows = $borrows;

        return $this;
    }

    /**
     * @todo a large part of the code below should be moved into DTO
     *
     * @param Loan $loan
     * @return $this
     */
    public function addBorrow(Loan $loan)
    {
        if (!$loan->getBorrower()) {
            $loan->setBorrower($this);
        }

        if ($loan->getBorrower() !== $this
        ) {
            throw new InvalidArgumentException('A borrow can be added to reader s borrow list only if he is the borrower in the Loan object');
        }

        if (!$loan->getBook()) {
            throw new InvalidArgumentException('A borrow be added to reader s borrow list only if the book is in the Loan object');
        }

        if ($this->borrows->contains($loan)) {
            return $this;
        }

        // @todo check if the book of the owner is available or already borrowed by someone: throw an exception to explain that it must be returned before it can be loaned again

        $this->borrows->add($loan);

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
        return (string) $this->getFirstname()
            . $this->getLastname();
    }
}
