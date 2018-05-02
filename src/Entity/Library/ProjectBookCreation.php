<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     attributes={"access_control"="is_granted('ROLE_USER')"}
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "book", "author"}, arguments={"orderParameterName"="order"})
 *
 * @ORM\Entity
 * @ORM\Table(name="project_book_creation")
 */
class ProjectBookCreation implements LibraryInterface
{
    /**
     * @Groups("book_detail_read")
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ApiSubresource()
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Library\Job", cascade={"persist"})
     * @ORM\JoinColumn(name="job_id", referencedColumnName="id")
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
     * @ApiSubresource(maxDepth=1)
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Author",
     *     inversedBy="books",
     *     fetch="EAGER",
     *     cascade={"persist", "remove"}
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
     * @param mixed $id
     * @return ProjectBookCreation
     */
    public function setId($id): ProjectBookCreation
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Job
     */
    public function getRole(): Job
    {
        return $this->role;
    }

    /**
     * @param Job $role
     * @return ProjectBookCreation
     */
    public function setRole(Job $role): ProjectBookCreation
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Book
     */
    public function getBook(): ?Book
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

    /**
     * Mandatory for EasyAdminBundle to build the select box
     * It also helps to build a footprint of the object, even if with the Serializer component it might be more pertinent
     *
     * @return string
     */
    public function __toString(): string
    {
        return ($this->getBook() ? $this->getBook()->getTitle() . ', ' : '')
            . $this->getAuthor()->__toString() . ' '
            . $this->getRole()->__toString();
    }
}
