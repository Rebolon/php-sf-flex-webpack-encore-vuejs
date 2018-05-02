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
 *              "groups"={"book_detail_read"}
 *          },
 *          "denormalization_context"={
 *              "groups"={"book_detail_write"}
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
     * @Groups({"book_detail_read"})
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
     * @Groups({"book_detail_read", "book_detail_write"})
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
     * @Groups({"book_detail_read", "book_detail_write"})
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
     * @Groups({"book_detail_read", "book_detail_write"})
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
     * @var ArrayCollection of ProjectBookCreation
     *
     * @ApiSubresource(maxDepth=1)
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookCreation", mappedBy="book", cascade={"persist", "remove"})
     */
    private $authors;

    /**
     * @var ArrayCollection of ProjectBookEdition
     *
     * @ApiSubresource(maxDepth=1)
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookEdition", mappedBy="book", cascade={"persist", "remove"})
     */
    private $editors;

    /**
     * @ApiSubresource(maxDepth=1)
     * @Groups({"book_detail_read", "book_detail_write"})
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
     * @return Book
     */
    public function setReviews(ArrayCollection $reviews): Book
    {
        $this->reviews = $reviews;

        return $this;
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
     * @param ArrayCollection|ArrayCollection $projects
     *
     * @return Book
     */
    public function setAuthors($projects): Book
    {
        $this->authors->clear();

        foreach($projects as $project) {
            $this->addAuthors($project);
        }

        return $this;
    }

    /**
     * @param ProjectBookCreation $project
     *
     * @return Book
     */
    public function addAuthors(ProjectBookCreation $project): Book
    {
        // Take care that contains will just do an in_array strict check
        if ($this->hasProjectBookCreation($project)) {
            return $this;
        }

        $project->setBook($this); // mandatory
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

        $this->addAuthors($project);

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
     * @param array|ArrayCollection $projects
     * @return Book
     */
    public function setEditors($projects): Book
    {
        $this->editors->clear();

        foreach($projects as $project) {
            $this->addEditors($project);
        }

        return $this;
    }

    /**
     * @param ProjectBookEdition $project
     * @return Book
     */
    public function addEditors(ProjectBookEdition $project): Book
    {
        if ($this->hasProjectBookEdition($project)) {
            return $this;
        }

        $project->setBook($this); // mandatory
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

        $this->addEditors($project);

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
     * Better than ArrayCollection->contains(object) that only does an in_array strict check
     * Always find a way to distinguish your Entities:
     *  * if they are already persisted, the ID is the best solution
     *  * or use a __toString() that will build the footprint of your object
     *
     * With the if condition we block homonyme author, it's maybe not the wished behaviour. But it would be easy in real
     * world to distinguish author: add extra info like nationality, birthdate, sex, ...
     * We could also decide to check only on ID if it exists, in that cas :
     *  * there is an ID => i can add it, so for homonym you would have to create it first and add it after
     *  * there is no ID => i check with __toString()
     *
     * @param ProjectBookCreation $project
     * @return bool
     */
    protected function hasProjectBookCreation(ProjectBookCreation $project)
    {
        foreach ($this->authors as $projectToCheck) {
            if (
                (
                    !is_null($project->getAuthor()->getId())
                    && $projectToCheck->getAuthor()->getId() === $project->getAuthor()->getId()
                    || $projectToCheck->getAuthor()->__toString() === $project->getAuthor()->__toString()
                ) && (
                    !is_null($project->getRole()->getId())
                    && $projectToCheck->getRole()->getId() === $project->getRole()->getId()
                    || $projectToCheck->getRole()->__toString() === $project->getRole()->__toString()
                )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Better than ArrayCollection->contains(object) that only does an in_array strict check
     * Always find a way to distinguish your Entities:
     *  * if they are already persisted, the ID is the best solution
     *  * or use a __toString() that will build the footprint of your object
     *
     * With the if condition we block homonyme author, it's maybe not the wished behaviour. But it would be easy in real
     * world to distinguish author: add extra info like nationality, birthdate, sex, ...
     * We could also decide to check only on ID if it exists, in that cas :
     *  * there is an ID => i can add it, so for homonym you would have to create it first and add it after
     *  * there is no ID => i check with __toString()
     *
     * @param ProjectBookEdition $project
     * @return bool
     */
    protected function hasProjectBookEdition(ProjectBookEdition $project)
    {
        foreach ($this->editors as $projectToCheck) {
            if (
                !is_null($project->getEditor()->getId())
                && $projectToCheck->getEditor()->getId() === $project->getEditor()->getId()
                || $projectToCheck->__toString() === $project->__toString()
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
        return $this->getTitle()
            . ($this->getDescription() ? ', ' . $this->getDescription() : '')
            . (!is_null($this->getIndexInSerie()) ? ', #' . $this->getIndexInSerie() : '')
            ;
    }
}
