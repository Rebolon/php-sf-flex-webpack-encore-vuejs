<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ApiResource(iri="http://bib.schema.org/ComicStory")
 * @ORM\Entity
 */
class Book
{
    /**
     * @ApiProperty(
     *     iri="http://schema.org/identifier"
     * )
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
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/description"
     * )
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
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $index_in_serie;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/reviews"
     * )
     * @ApiSubresource(maxDepth=1)
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\Review", mappedBy="book", orphanRemoval=true)
     */
    private $reviews;

    /**
     * @var ProjectBookCreation
     *
     * @ApiSubresource(maxDepth=1)
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookCreation", mappedBy="book", cascade={"persist", "remove"})
     */
    private $authors;

    /**
     * @var ProjectBookEdition
     *
     * @ApiSubresource(maxDepth=1)
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookEdition", mappedBy="book", cascade={"persist", "remove"})
     */
    private $editors;

    /**
     * @ApiSubresource(maxDepth=1)
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Library\Serie", inversedBy="book")
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
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle(): string
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
        return $this->index_in_serie;
    }

    /**
     * @param mixed $indexInSerie
     * @return Book
     */
    public function setIndexInSerie($indexInSerie): Book
    {
        $this->index_in_serie = $indexInSerie;

        return $this;
    }

    /**
     * @return PersistentCollection
     */
    public function getReviews(): PersistentCollection
    {
        return $this->reviews;
    }

//    /**
//     * @return PersistentCollection
//     */
//    public function getProjectBookCreation(): PersistentCollection
//    {
//        return $this->projectBookCreation;
//    }

//    /**
//     * @return PersistentCollection
//     */
//    public function getProjectBookEdition(): PersistentCollection
//    {
//        return $this->projectBookEdition;
//    }

    /**
     * @return Serie
     */
    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    /**
     * @return $this
     */
    public function setSerie(Serie $serie): Book
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * @param ProjectBookCreation $project
     * @return $this
     */
    public function setAuthor(ProjectBookCreation $project): Book
    {
        $this->authors[] = $project;

        return $this;
    }

    /**
     * @param Author $author
     * @param Job $job
     * @return $this
     */
    public function addAuthor(Author $author, Job $job): Book
    {
        $project = (new ProjectBookCreation())
            ->setBook($this)
            ->setAuthor($author)
            ->setRole($job->getId());

        // @test this feature to check that it really works vs if ($this->projectBookCreation->contains($project)) return $this;
        foreach ($this->authors as $projectToCheck) {
            if ($projectToCheck->getAuthor() === $author
                && $projectToCheck->role === $job->getId()) {
                return $this;
            }
        }

        $this->setAuthor($project);

        return $this;
    }

    /**
     * Return the list of Authors with their job for this project book creation
     *
     * @return PersistentCollection
     */
    public function getAuthors(): PersistentCollection
    {
        // @todo list ProjectBookCreation with fields id/role/author (book should be omitted to prevent circular reference)
        return $this->authors;
    }

    /**
     * @param ProjectBookEdition $project
     * @return $this
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
     * @return PersistentCollection
     */
    public function getEditors(): PersistentCollection
    {
        //@todo list ProjectBookEdition with fields id/publicationdate/collection/isbn/editor (book should be omitted to prevent circular reference)
        return $this->editors;
    }
}
