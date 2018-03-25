<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={"access_control"="is_granted('ROLE_USER')"}
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "book", "editor", "publicationDate", "isbn", "collection"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(DateFilter::class, properties={"publicationDate"})
 * @ORM\Entity
 * @ORM\Table(name="project_book_edition")
 */
class ProjectBookEdition implements LibraryInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true, options={"default":"now()"}, name="publication_date")
     * @Assert\DateTime()
     */
    private $publicationDate;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Type(type="string")
     */
    private $collection;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/isbn"
     * )
     * @ORM\Column(nullable=true)
     * @Assert\Isbn()
     */
    private $isbn;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Editor",
     *     inversedBy="books",
     *     fetch="EAGER",
     *     cascade={"remove", "persist"}
     * )
     * @ORM\JoinColumn(name="editor_id", referencedColumnName="id")
     */
    private $editor;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Book",
     *     inversedBy="editors",
     *     fetch="EAGER",
     *     cascade={"remove", "persist"}
     * )
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     */
    private $book;

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
     * @return \DateTime
     */
    public function getPublicationDate(): \DateTime
    {
        return $this->publicationDate;
    }

    /**
     * @param mixed $publication_date
     * @return ProjectBookEdition
     */
    public function setPublicationDate($publicationDate): ProjectBookEdition
    {
        if (is_string($publicationDate)) {
            try {
                $publicationDate = new \DateTime($publicationDate);
            } catch (\Exception $e) {
                $dateTime = new \DateTime();
                $publicationDate = $dateTime->setTimestamp($publicationDate);
            }
        }

        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getCollection(): ?string
    {
        return $this->collection;
    }

    /**
     * @param mixed $collection
     * @return ProjectBookEdition
     */
    public function setCollection($collection): ProjectBookEdition
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @return string
     */
    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    /**
     * @param mixed $isbn
     *
     * @return ProjectBookEdition
     */
    public function setIsbn($isbn): ProjectBookEdition
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * @return Editor
     */
    public function getEditor(): Editor
    {
        return $this->editor;
    }

    /**
     * @param Editor $editor
     * @return $this
     */
    public function setEditor(Editor $editor): ProjectBookEdition
    {
        $this->editor = $editor;

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
    public function setBook(Book $book): ProjectBookEdition
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Mandatory for EasyAdminBundle to build the select box
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getBook()->getTitle() . ' ' . $this->getEditor()->getName() . ' ' . $this->getCollection();
    }
}
