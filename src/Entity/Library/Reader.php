<?php
namespace App\Entity\Library;

use App\Entity\LoggerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ReaderRepository;

/**
 * @ORM\Entity(repositoryClass="ReaderRepository")
 */
class Reader implements LibraryInterface
{
    use LoggerTrait;

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
    private $id;

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
    private $lastname;

    /**
     * @Groups({"reader_read", "reader_write"})
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    private $firstname;

    /**
     * @todo it may not be a list of books but a list of projectEdition coz you may get a book more than once but in
     * different edition ! For instance i keep this implementation for the sample but i might improve this in future
     *
     * @Groups({"reader_read", "reader_write"})
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Library\Book")
     *
     * @var Collection|Book[]
     */
    private $myLibrary;

    /**
     * Book constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);

        $this->myLibrary = new ArrayCollection();
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
