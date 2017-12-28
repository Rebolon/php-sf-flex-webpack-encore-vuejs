<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="project_book_edition")
 */
class ProjectBookEdition
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true, options={"default":"now()"})
     */
    private $publication_date;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $collection;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/isbn"
     * )
     * @ORM\Column(nullable=true)
     * @Assert\Isbn
     */
    private $isbn;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Editor",
     *     inversedBy="books",
     *     fetch="EAGER",
     *     cascade={"remove"}
     * )
     * @ORM\JoinColumn(name="editor_id", referencedColumnName="id")
     */
    private $editor;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Book",
     *     inversedBy="editors",
     *     fetch="EAGER",
     *     cascade={"remove"}
     * )
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     */
    private $book;

    /**
     * @return \DateTime
     */
    public function getPublicationDate(): \DateTime
    {
        return $this->publication_date;
    }

    /**
     * @param mixed $publication_date
     * @return ProjectBookEdition
     */
    public function setPublicationDate($publication_date): ProjectBookEdition
    {
        $this->publication_date = $publication_date;

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
}
