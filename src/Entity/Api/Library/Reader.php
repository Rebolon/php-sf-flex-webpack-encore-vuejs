<?php
namespace App\Entity\Api\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use App\Entity\Library\Book;
use App\Entity\Library\LibraryInterface;
use App\Entity\Library\Loan;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource(
 *     iri="http://bib.schema.org/user",
 *     collectionOperations={
 *          "get"={"method"="GET", "access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *          "post"={"method"="POST", "access_control"="is_granted('ROLE_ADMIN', 'ROLE_USER')", "access_control_message"="Only admin users can add users, or the user himself for their own informations."}
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_ADMIN', 'ROLE_USER')", "access_control_message"="Only admin users can modify users, or the user himself for their own informations."},
 *         "delete"={"method"="delete", "access_control"="is_granted('ROLE_ADMIN', 'ROLE_USER')", "access_control_message"="Only admin users can delete users, or the user himself for his own informations."}
 *     },
 *     attributes={
 *          "normalization_context"={
 *              "groups"={"reader_read"}
 *          },
 *          "denormalization_context"={
 *              "groups"={"reader_write"}
 *          }
 *     }
 * )
 * @ ApiFilter(OrderFilter::class, properties={"id", "lastname"})
 * @ ApiFilter(SearchFilter::class, properties={"id": "exact", "lastname": "istart", "firstname": "istart"})
 */
class Reader implements LibraryInterface
{
    /**
     * @ApiProperty(
     *     identifier=true,
     *     iri="http://schema.org/identifier"
     * )
     * @Groups({"reader_read"})
     * @var int
     */
    protected $id;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/lastname"
     * )
     * @Groups({"reader_read", "reader_write"})
     * @var string
     */
    protected $lastname;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/description"
     * )
     * @Groups({"reader_read", "reader_write"})
     * @var string
     */
    protected $firstname;

    /**
     * @todo it may not be a list of books but a list of projectEdition coz you may get a book more than once but in
     * different edition ! For instance i keep this implementation for the sample but i might improve this in future
     *
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @Groups({"reader_read", "reader_write"})
     *
     * @var Book[]|Collection
     */
    protected $books;

    /**
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @Groups({"reader_read"})
     *
     * @var Loan[]|Collection
     */
    protected $loans;

    /**
     * Reader constructor.
     */
    public function __construct()
    {
        $this->books = new ArrayCollection();
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
     * @param mixed $lastname
     * @return self
     */
    public function setLastname($lastname): self
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
    public function setFirstname($firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return Book[]|Collection
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @param Collection $aLibrary
     * @return self
     */
    public function setBooks(Collection $aLibrary): self
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
     * @param Book $book
     * @return bool
     */
    protected function hasBookInBooks(Book $book): bool
    {
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
    public function setLoans($loans)
    {
        $this->loans = $loans;

        return $this;
    }

    /**
     * @param Loan $loan
     * @return $this
     */
    public function addLoan(Loan $loan)
    {
        if ($loan->getLoaner() !== $this
                && $loan->getBorrower() !== $this
        ) {
            throw new InvalidArgumentException('A loan can be added to reader s loan list only if he is the loaner or the borrower in the Loan object');
        }

        if (!$loan->getBook()) {
            throw new InvalidArgumentException('A loan can be added to reader s loan list only if the book is in the Loan object');
        }

        if ($loan->getLoaner() === $this
        && !$this->books->contains($loan->getBook())) {
            throw new InvalidArgumentException('A loan can be added to reader s loan list only if the book exists in the reader s books collection');
        }

        if ($this->loans->contains($loan)) {
            return $this;
        }

        // @todo check if the book of the owner is available or already borrowed by someone: throw an exception to explain that it must be returned before it can be loaned again

        $this->loans->add($loan);

        return $this;
    }
}
