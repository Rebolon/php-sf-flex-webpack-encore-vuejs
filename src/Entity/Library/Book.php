<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     iri="http://bib.schema.org/ComicStory",
 *     collectionOperations={
 *          "get"={"method"="GET"},
 *          "post"={"method"="POST", "access_control"="is_granted('ROLE_USER')", "access_control_message"="Only authenticated users can add books."},
 *          "special_3"={"route_name"="book_special_sample3", "access_control"="is_granted('ROLE_USER')", "access_control_message"="Only authenticated users can add books."},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_USER')", "access_control_message"="Only authenticated users can modify books."},
 *         "delete"={"method"="delete", "access_control"="is_granted('ROLE_USER')", "access_control_message"="Only authenticated users can delete books."},
 *         "special_1"={"route_name"="book_special_sample1"},
 *         "special_2"={"route_name"="book_special_sample2"},
 *     },
 *     attributes={
 *          "normalization_context"={
 *              "groups"={"book_detail"}
 *          },
 *          "denormalization_context"={
 *              "groups"={"book_detail"}
 *          }
 *     }
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "title"}, arguments={"orderParameterName"="order"})
 *
 * @ORM\Entity
 */
class Book implements LibraryInterface
{
    /**
     * @ApiProperty(
     *     iri="http://schema.org/identifier"
     * )
     * @Groups("book_detail")
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/headline"
     * )
     * @Groups("book_detail")
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     *
     */
    private $title;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/description"
     * )
     * @Groups("book_detail")
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/position",
     *     attributes={
     *         "jsonld_context"={
     *             "@type"="http://www.w3.org/2001/XMLSchema#integer"
     *         }
     *     }
     * )
     * @Groups("book_detail")
     *
     * @ORM\Column(type="integer", nullable=true, name="index_in_serie")
     *
     * @Assert\Type(type="integer")
     */
    private $indexInSerie;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/reviews"
     * )
     *
     * @ApiSubresource(maxDepth=1)
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\Review", mappedBy="book", orphanRemoval=true)
     */
    private $reviews;

    /**
     * @var ProjectBookCreation
     *
     * @ApiSubresource(maxDepth=1)
     * @Groups("book_detail")
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookCreation", mappedBy="book", cascade={"persist", "remove"})
     */
    private $authors;

    /**
     * @var ProjectBookEdition
     *
     * @ApiSubresource(maxDepth=1)
     * @Groups("book_detail")
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookEdition", mappedBy="book", cascade={"persist", "remove"})
     */
    private $editors;

    /**
     * @ApiSubresource(maxDepth=1)
     * @Groups("book_detail")
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Library\Serie", inversedBy="books", cascade={"persist"})
     * @ORM\JoinColumn(name="serie_id", referencedColumnName="id")
     */
    private $serie;

    /**
     * Book constructor.
     */
    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->editors = new ArrayCollection();
    }

    /**
     * id can be null until flush is done
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Book
     */
    public function setId($id): Book
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Book
     */
    public function setTitle($title): Book
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return Book
     */
    public function setDescription($description): Book
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIndexInSerie(): ?int
    {
        return $this->indexInSerie;
    }

    /**
     * @param mixed $indexInSerie
     * @return Book
     */
    public function setIndexInSerie($indexInSerie): Book
    {
        $this->indexInSerie = $indexInSerie;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }
    /**
     * @param Review $review
     * @return Book
     */
    public function addReview(Review $review): Book
    {
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * @return Serie
     */
    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    /**
     * @param Serie $serie
     *
     * @return Book
     */
    public function setSerie(Serie $serie): Book
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * @param ProjectBookCreation $project
     *
     * @return Book
     */
    public function setAuthor(ProjectBookCreation $project): Book
    {
        $this->authors[] = $project;

        return $this;
    }

    /**
     * @param Author $author
     * @param Job $job
     * @return Book
     */
    public function addAuthor(Author $author, Job $job): Book
    {
        $project = (new ProjectBookCreation())
            ->setBook($this)
            ->setAuthor($author)
            ->setRole($job);

        // @test this feature to check that it really works vs if ($this->projectBookCreation->contains($project)) return $this;
        foreach ($this->authors as $projectToCheck) {
            if ($projectToCheck->getAuthor() === $author
                && $projectToCheck->role === $job) {
                return $this;
            }
        }

        $this->setAuthor($project);

        return $this;
    }

    /**
     * Return the list of Authors with their job for this project book creation
     *
     * @return Collection
     */
    public function getAuthors(): Collection
    {
        // @todo list ProjectBookCreation with fields id/role/author (book should be omitted to prevent circular reference)
        return $this->authors;
    }

    /**
     * @param ProjectBookEdition $project
     * @return Book
     */
    public function setEditor(ProjectBookEdition $project): Book
    {
        $this->editors[] = $project;

        return $this;
    }

    /**
     * @param Editor $editor
     * @param \DateTime $date
     * @param null $isbn
     * @param null $collection
     * @return $this
     */
    public function addEditor(Editor $editor, \DateTime $date, $isbn = null, $collection = null): Book
    {
        $project = (new ProjectBookEdition())
            ->setBook($this)
            ->setEditor($editor)
            ->setPublicationDate($date)
            ->setIsbn($isbn)
            ->setCollection($collection);

        // @todo test this feature to check that it really works vs if ($this->projectBookEdition->contains($project)) return $this;
        foreach ($this->editors as $projectToCheck) {
            if ($projectToCheck->getEditor() === $editor
                && $projectToCheck->getPublicationDate() === $date
                && $projectToCheck->getISBN() === $isbn
                && $projectToCheck->getCollection() === $collection) {
                return $this;
            }
        }

        $this->setEditor($project);

        return $this;
    }

    /**
     * @todo the content of the methods + the route mapping for the api
     * Return the list of Editors for all projects book edition of this book
     *
     * @return Collection
     */
    public function getEditors(): Collection
    {
        //@todo list ProjectBookEdition with fields id/publicationdate/collection/isbn/editor (book should be omitted to prevent circular reference)
        return $this->editors;
    }

    /**
     * Mandatory for EasyAdminBundle to build the select box
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
