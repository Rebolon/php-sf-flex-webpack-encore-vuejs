<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

/**
 * @ApiResource(
 *     attributes={
 *          "access_control"="is_granted('ROLE_USER')",
 *          "pagination_client_enabled"=true
 *      }
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "book", "editor", "publicationDate", "isbn", "collection"})
 * @ApiFilter(DateFilter::class, properties={"publicationDate"})
 *
 * @ORM\Entity
 * @ORM\Table(name="project_book_edition")
 */
class ProjectBookEdition implements LibraryInterface
{
    /**
     * @Groups("book_detail_read")
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
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\Column(type="date", nullable=true, name="publication_date")
     *
     * @Assert\DateTime()
     *
     * @var DateTime
     */
    protected $publicationDate;

    /**
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Type(type="string")
     *
     * @var string
     */
    protected $collection;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/isbn"
     * )
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\Column(nullable=true)
     *
     * @Assert\Isbn()
     *
     * @var string
     */
    protected $isbn;

    /**
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Editor",
     *     inversedBy="books",
     *     fetch="EAGER",
     *     cascade={"remove", "persist"}
     * )
     * @ORM\JoinColumn(name="editor_id", referencedColumnName="id")
     *
     * @var Editor
     */
    protected $editor;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Book",
     *     inversedBy="editors",
     *     fetch="EAGER",
     *     cascade={"remove", "persist"}
     * )
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     *
     * @var Book
     */
    protected $book;

    /**
     * ProjectBookEdition constructor.
     */
    public function __construct()
    {
        // default value coz since mid-2018 (don't have exact DBAL version) there is no more default attributes for datetime
        $this->setPublicationDate(new DateTime());
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
     * @return DateTime
     */
    public function getPublicationDate(): DateTime
    {
        return $this->publicationDate;
    }

    /**
     * @param DateTime|string $publicationDate
     * @return self
     */
    public function setPublicationDate($publicationDate): self
    {
        // @todo mutualize this code
        if (is_string($publicationDate)) {
            $dateString = $publicationDate;
            try {
                if (preg_match('/\d*/', $publicationDate)) {
                    $dateTime = new DateTime();
                    $publicationDate = $dateTime->setTimestamp((int) $publicationDate);
                } else {
                    $publicationDate = new DateTime($publicationDate);
                }
            } catch (\Exception $e) {
                $this->logger->warning(sprintf('Wrong input for publicationDate, %s', $dateString));
            }
        } elseif (!($publicationDate instanceof DateTime)) {
            $this->logger->warning(sprintf(
                'Wrong input for publicationDate, should be \DateTime or valid date string or unixTimestamp, %s',
                is_object($publicationDate) ? $publicationDate->__toString() : $publicationDate
            ));
        }

        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCollection(): ?string
    {
        return $this->collection;
    }

    /**
     * @param mixed $collection
     * @return self
     */
    public function setCollection($collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    /**
     * @param mixed $isbn
     *
     * @return self
     */
    public function setIsbn($isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * @return Editor|null null in the case that the editor has been removed, we may should add a log on this
     */
    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    /**
     * @param Editor $editor
     * @return self
     */
    public function setEditor(Editor $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @return Book|null
     */
    public function getBook(): ?Book
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
     * Mandatory for EasyAdminBundle to build the select box
     * It also helps to build a footprint of the object, even if with the Serializer component it might be more pertinent
     *
     * @return string
     */
    public function __toString(): string
    {
        return ($this->getBook() ? $this->getBook()->getTitle() . ', ' : '')
            . $this->getEditor()->__toString() . ' '
            . ($this->getPublicationDate() ? ', ' . $this->getPublicationDate()->format('Ymd') : '')
            . ($this->getCollection() ? ', ' . $this->getCollection() : '')
            . ($this->getIsbn() ? ', #' . $this->getIsbn() : '');

    }
}
