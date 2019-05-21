<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Library\ReaderRepository")
 */
class Reader implements LibraryInterface
{
    /**
     * @Groups({"reader_read"})
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
     * @Groups({"reader_read", "reader_write"})
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     *
     * @var string
     */
    protected $lastname;

    /**
     * @Groups({"reader_read", "reader_write"})
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $firstname;

    /**
     * @todo it may not be a list of books but a list of projectEdition coz you may get a book more than once but in
     * different edition ! For instance i keep this implementation for the sample but i might improve this in future
     *
     * @Groups({"reader_read", "reader_write"})
     * @MaxDepth(1)
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Library\Book")
     *
     * @var Collection|Book[]
     */
    protected $myLibrary;

    /**
     * List of book a reader has borrowed to another reader
     *
     * @Groups({"reader_read", "reader_write"})
     * @MaxDepth(1)
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\Loan", mappedBy="loaner")
     * @var Collection|Loan[]
     */
    protected $loans;

    /**
     * List of book a reader has borrowed from another reader
     *
     * @Groups({"reader_read", "reader_write"})
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
        $this->myLibrary = new ArrayCollection();
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
     * @return Collection|Book[]
     */
    public function getMyLibrary(): Collection
    {
        return $this->myLibrary;
    }

    /**
     * @param ArrayCollection $aLibrary
     * @return self
     */
    public function setMyLibrary(ArrayCollection $aLibrary): self
    {
        $this->myLibrary = $aLibrary;

        return $this;
    }

    /**
     * @param Book $book
     * @return self
     */
    public function addMyLibrary(Book $book): self
    {
        if ($this->hasBookInMyLibrary($book)) {
            return $this;
        }

        $this->myLibrary[] = $book;

        return $this;
    }

    /**
     * @param Book $book
     * @return bool
     */
    protected function hasBookInMyLibrary(Book $book): bool
    {
        // @todo check performance: it may be better to do a DQL to check instead of doctrine call to properties that may do new DB call
        foreach ($this->myLibrary as $bookIAlreadyGet) {
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
     * @param Loan $loan
     * @return $this
     */
    public function addLoan(Loan $loan)
    {
        if (!$loan->getLoaner()) {
            $loan->setLoaner($this);
        }

        if ($loan->getLoaner() !== $this) {
            throw new InvalidArgumentException('A reader can add a book to his loan list only if he is the loaner in the Loan object');
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
     * @param Loan $loan
     * @return $this
     */
    public function addBorrow(Loan $loan)
    {
        if (!$loan->getBorrower()) {
            $loan->setBorrower($this);
        }

        if ($loan->getBorrower() !== $this) {
            throw new InvalidArgumentException('A reader can add a book to his borrow list only if he is the borrower in the Loan object');
        }

        if ($this->borrows->contains($loan)) {
            return $this;
        }

        // @todo check if the book of the owner is available or already borrowed by someone: throw an exception to explain that it must be returned before it can be loaned again
        $this->

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
