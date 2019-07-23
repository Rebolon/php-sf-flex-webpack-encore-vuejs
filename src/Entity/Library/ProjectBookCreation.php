<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={
 *          "access_control"="is_granted('ROLE_USER')",
 *          "pagination_client_enabled"=true
 *      }
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "book", "author"})
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
     *
     * @Assert\Uuid()
     *
     * @var int
     */
    protected $id;

    /**
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Library\Job", cascade={"persist"})
     * @ORM\JoinColumn(name="job_id", referencedColumnName="id")
     *
     * @var Job
     */
    protected $role;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Book",
     *     inversedBy="authors",
     *     fetch="EAGER",
     *     cascade={"remove"}
     * )
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     *
     * @var Book
     */
    protected $book;

    /**
     * @MaxDepth(1)
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Author",
     *     inversedBy="books",
     *     fetch="EAGER",
     *     cascade={"persist", "remove"}
     * )
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     *
     * @var Author
     */
    protected $author;

    /**
     * ProjectBookCreation constructor.
     */
    public function __construct()
    {
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
     * @return Job
     */
    public function getRole(): Job
    {
        return $this->role;
    }

    /**
     * @param Job $role
     * @return self
     */
    public function setRole(Job $role): self
    {
        $this->role = $role;

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
     * @return Author|null
     */
    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    /**
     * @param Author $author
     * @return self
     */
    public function setAuthor(Author $author): self
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
