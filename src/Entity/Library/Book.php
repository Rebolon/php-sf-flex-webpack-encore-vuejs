<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource
 * @ORM\Entity
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $index_in_serie;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Library\Review", mappedBy="book", orphanRemoval=true)
     * @ApiSubresource(maxDepth=1)
     */
    private $reviews;

    /**
     * @var ProjectBookCreation
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookCreation", mappedBy="book")
     */
    private $projectBookCreation;

    /**
     * @var ProjectBookEdition
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookEdition", mappedBy="book")
     */
    private $projectBookEdition;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Library\Serie", inversedBy="book")
     * @ORM\JoinColumn(name="serie_id", referencedColumnName="id")
     * @ApiSubresource(maxDepth=1)
     */
    private $serie;

    /**
     * Book constructor.
     */
    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->projectBookCreation = new ArrayCollection();
        $this->projectBookEdition = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @param mixed $isbn
     * @return Book
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Book
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return Book
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIndexInSerie()
    {
        return $this->index_in_serie;
    }

    /**
     * @param mixed $indexInSerie
     * @return Book
     */
    public function setIndexInSerie($indexInSerie)
    {
        $this->index_in_serie = $indexInSerie;

        return $this;
    }

    /**
     * @return Collection|Reviews[]
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @return mixed
     */
    public function getProjectBookCreation()
    {
        return $this->projectBookCreation;
    }

    /**
     * @return mixed
     */
    public function getProjectBookEdition()
    {
        return $this->projectBookEdition;
    }

    /**
     * @return Serie
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * @param ProjectBookCreation $project
     * @return $this
     */
    public function setAuthor(ProjectBookCreation $project)
    {
        $this->projectBookCreation[] = $project;

        return $this;
    }

    /**
     * @param Author $author
     * @param int $role
     * @return $this
     */
    public function addAuthor(Author $author, int $role)
    {
        $project = (new ProjectBookCreation())
            ->setAuthor($author)
            ->setRole($role);

        // @test this feature to check that it really works
        foreach ($this->projectBookCreation as $projectToCheck) {
            if ($projectToCheck->author === $author
                && $projectToCheck->role === $role) {
                return;
            }
        }

        $this->setAuthor($project);

        return $this;
    }

    /**
     * Return the list of Authors with their job for this project book creation
     * @todo
     *
     * @return collection|ProjectBookCreation
     */
    public function getAuthors()
    {
        // @todo list ProjectBookCreation with fields id/role/author (book is omitted)
        return $this->projectBookCreation;
    }

    /**
     * @param ProjectBookEdition $project
     * @return $this
     */
    public function setEditor(ProjectBookEdition $project)
    {
        $this->projectBookEdition[] = $project;

        return $this;
    }

    /**
     * @param Editor $editor
     * @param \DateTime $date
     * @param null $isbn
     * @param null $collection
     * @return $this
     */
    public function addEditor(Editor $editor, \DateTime $date, $isbn = null, $collection = null)
    {
        $project = (new ProjectBookEdition())
            ->setEditor($editor)
            ->setDate($date)
            ->setIsbn($isbn)
            ->setCollection($collection);

        // @test this feature to check that it really works
        foreach ($this->projectBookEdition as $projectToCheck) {
            if ($projectToCheck->getEditor() === $editor
                && $projectToCheck->getPublicationDate() === $date
                && $projectToCheck->getISBN() === $isbn
                && $projectToCheck->getCollection() === $collection) {
                return;
            }
        }

        $this->setEditor($project);

        return $this;
    }

    /**
     * @todo the content of the methods + the route mapping for the api
     * Return the list of Editors for all projects book edition of this book
     *
     * @return collection|ProjectBookEdition
     */
    public function getEditors()
    {
        //@todo list ProjectBookEdition with fields id/publicationdate/collection/isbn/editor (book is omitted)
        return $this->projectBookEdition;
    }
}
