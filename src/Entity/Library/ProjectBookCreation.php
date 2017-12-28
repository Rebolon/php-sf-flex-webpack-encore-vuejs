<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity
 * @ORM\Table(name="project_book_creation")
 */
class ProjectBookCreation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * To be easier, should be a OneToOne ? with link on JobID
     * @ORM\Column(type="integer", nullable=true)
     */
    private $role;

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
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Author",
     *     inversedBy="books",
     *     fetch="EAGER",
     *     cascade={"remove"}
     * )
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

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
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     * @return ProjectBookCreation
     */
    public function setRole($role): ProjectBookCreation
    {
        $this->role = $role;

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
    public function setBook(Book $book): ProjectBookCreation
    {
        $this->book = $book;

        return $this;
    }

    /**
     * @return Author
     */
    public function getAuthor(): Author
    {
        return $this->author;
    }

    /**
     * @param Author $author
     * @return $this
     */
    public function setAuthor(Author $author): ProjectBookCreation
    {
        $this->author = $author;

        return $this;
    }
}
