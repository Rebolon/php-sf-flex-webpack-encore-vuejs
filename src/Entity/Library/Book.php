<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     iri="http://bib.schema.org/ComicStory",
 *     security="is_granted('ROLE_USER')",
 *
 *     normalizationContext={
 *         "groups"={"book:detail:read"}
 *     },
 *     denormalizationContext={
 *         "groups"={"book:detail:write"}
 *     },
 *     paginationClientEnabled=true,
 *     collectionOperations={
 *          "get",
 *          "post"={"security"="is_granted('ROLE_USER')", "securityMessage"="Only authenticated users can add books."},
 *          "special_3"={"method"="POST", "route_name"="book_special_sample3", "security"="is_granted('ROLE_USER')", "securityMessage"="Only authenticated users can add books."},
 *     },
 *     itemOperations={
 *         "get",
 *         "put"={"security"="is_granted('ROLE_USER')", "securityMessage"="Only authenticated users can modify books."},
 *         "delete"={"security"="is_granted('ROLE_USER')", "securityMessage"="Only authenticated users can delete books."}
 *     }
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "title"})
 * @ApiFilter(SearchFilter::class, properties={"title": "istart", "description": "partial", "tags.name"="exact"})
 * @ApiFilter(PropertyFilter::class, arguments={"parameterName": "properties", "overrideDefaultProperties": false}))
 *
 * @ORM\Entity(repositoryClass="App\Repository\Library\BookRepository")
 */
class Book implements LibraryInterface
{
    /**
     * @ApiProperty(
     *     iri="http://schema.org/identifier"
     * )
     * @Groups({"book:detail:read", "reader:read"})
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var ?int
     */
    protected ?int $id = null;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/headline"
     * )
     * @Groups({"book:detail:read", "book:detail:write", "reader:read"})
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     *
     * @var string
     */
    protected string $title;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/description"
     * )
     * @Groups({"book:detail:read", "book:detail:write", "reader:read"})
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    protected ?string $description;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/position",
     *     attributes={
     *         "jsonld_context"={
     *             "@type"="http://www.w3.org/2001/XMLSchema#integer"
     *         }
     *     }
     * )
     * @Groups({"book:detail:read", "book:detail:write", "reader:read"})
     *
     * @ORM\Column(type="integer", nullable=true, name="index_in_serie")
     *
     * @Assert\Type(type="integer")
     *
     * @var int|null
     */
    protected ?int $indexInSerie;

    /**
     * @var Collection|Review[]
     *
     * @ApiProperty(
     *     iri="http://schema.org/reviews"
     * )
     *
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\Review", mappedBy="book", orphanRemoval=true, cascade={"persist", "remove"})
     */
    protected Collection $reviews;

    /**
     * @var Collection|ProjectBookCreation[]
     *
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @Groups({"book:detail:read", "book:detail:write", "reader:read"})
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookCreation", mappedBy="book", cascade={"persist", "remove"})
     */
    protected Collection $authors;

    /**
     * @var Collection|ProjectBookEdition[]
     *
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @Groups({"book:detail:read", "book:detail:write", "reader:read"})
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookEdition", mappedBy="book", cascade={"persist", "remove"})
     */
    protected Collection $editors;

    /**
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @Groups({"book:detail:read", "book:detail:write", "reader:read"})
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Library\Serie", inversedBy="books", cascade={"persist"})
     * @ORM\JoinColumn(name="serie_id", referencedColumnName="id")
     *
     * @var Serie
     */
    protected Serie $serie;

    /**
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @Groups({"book:detail:read", "book:detail:write", "reader:read"})
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Library\Tag", inversedBy="books", cascade={"persist"})
     *
     * @var Collection|Tag[]
     */
    protected Collection $tags;

    /**
     * @ApiSubresource(maxDepth=1)
     *
     * @MaxDepth(1)
     * @Groups({"reader:read"})
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\Loan", mappedBy="book")
     *
     * @var Collection|Loan[]
     */
    protected Collection $loans;

    /**
     * Book constructor.
     */
    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->editors = new ArrayCollection();
        $this->loans = new ArrayCollection();
    }

    /**
     * id can be null until flush is done
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
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return self
     */
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return self
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getIndexInSerie(): ?int
    {
        return $this->indexInSerie;
    }

    /**
     * @param mixed $indexInSerie
     * @return self
     */
    public function setIndexInSerie($indexInSerie): self
    {
        $this->indexInSerie = $indexInSerie;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Tag[]|Collection $tags
     * @param bool $updateRelation
     * @return self
     */
    public function setTags(Collection $tags, bool $updateRelation = true): self
    {
        $this->tags->clear();

        foreach ($tags as $tag) {
            $this->addTag($tag, $updateRelation);
        }

        return $this;
    }

    /**
     * @param Tag $tag
     * @param bool $updateRelation
     * @return self
     */
    public function addTag(Tag $tag, bool $updateRelation = true): self
    {
        if ($this->tags->contains($tag)) {
            return $this;
        }

        if ($updateRelation) {
            $tag->addBook($this, false);
        }

        $this->tags[] = $tag;

        return $this;
    }

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    /**
     * @param Review[]|Collection $reviews
     * @param bool $updateRelation
     * @return self
     */
    public function setReviews(Collection $reviews, bool $updateRelation = true): self
    {
        $this->reviews->clear();

        foreach ($reviews as $review) {
            $this->addReview($review, $updateRelation);
        }

        return $this;
    }

    /**
     * @param Review $review
     * @param bool $updateRelation
     * @return self
     */
    public function addReview(Review $review, bool $updateRelation = true): self
    {
        if ($this->reviews->contains($review)) {
            return $this;
        }

        if ($updateRelation) {
            $review->setBook($this, false);
        }

        $this->reviews[] = $review;

        return $this;
    }

    /**
     * @return Serie|null
     */
    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    /**
     * @param Serie $serie
     * @param bool $updateRelation
     * @return self
     */
    public function setSerie(Serie $serie, bool $updateRelation = true): self
    {
        if ($updateRelation) {
            $serie->addBook($this, false);
        }

        $this->serie = $serie;

        return $this;
    }

    /**
     * @param Collection $projects
     *
     * @return self
     */
    public function setAuthors(Collection $projects): self
    {
        $this->authors->clear();

        foreach ($projects as $project) {
            $this->addAuthors($project);
        }

        return $this;
    }

    /**
     * @param ProjectBookCreation $project
     *
     * @return self
     */
    public function addAuthors(ProjectBookCreation $project): self
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
     * @return self
     */
    public function addAuthor(Author $author, Job $job): self
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
     * @return Collection|ProjectBookCreation[]
     */
    public function getAuthors(): Collection
    {
        // @todo list ProjectBookCreation with fields id/role/author (book should be omitted to prevent circular reference)
        return $this->authors;
    }

    /**
     * @param Collection|ProjectBookEdition[] $projects
     * @return self
     */
    public function setEditors($projects): self
    {
        $this->editors->clear();

        foreach ($projects as $project) {
            $this->addEditors($project);
        }

        return $this;
    }

    /**
     * @param ProjectBookEdition $project
     * @return self
     */
    public function addEditors(ProjectBookEdition $project): self
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
     * @param DateTime $date
     * @param string $isbn
     * @param string $collection
     * @return self
     */
    public function addEditor(Editor $editor, DateTime $date, $isbn = null, $collection = null): self
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
     * @return Collection|ProjectBookEdition[]
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
        // @todo check performance: it may be better to do a DQL to check instead of doctrine call to properties that may do new DB call
        foreach ($this->authors as $projectToCheck) {
            if (
                (
                    (!is_null($project->getAuthor()->getId())
                    && $projectToCheck->getAuthor()->getId() === $project->getAuthor()->getId())
                    || $projectToCheck->getAuthor()->__toString() === $project->getAuthor()->__toString()
                ) && (
                    (!is_null($project->getRole()->getId())
                    && $projectToCheck->getRole()->getId() === $project->getRole()->getId())
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
        // @todo check performance: it may be better to do a DQL to check instead of doctrine call to properties that may do new DB call
        foreach ($this->editors as $projectToCheck) {
            if (
                (!is_null($project->getEditor()->getId())
                && $projectToCheck->getEditor()->getId() === $project->getEditor()->getId())
                || $projectToCheck->__toString() === $project->__toString()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Loan[]|Collection
     */
    public function getLoans()
    {
        return $this->loans;
    }

    /**
     * @param Loan[]|Collection $loans
     * @param bool $updateRelation
     * @return $this
     */
    public function setLoans(Collection $loans, bool $updateRelation = true): self
    {
        $this->loans->clear();

        foreach ($loans as $loan) {
            $this->addLoan($loan, $updateRelation);
        }

        return $this;
    }

    /**
     * @param Loan $loan
     * @param bool $updateRelation
     * @return $this
     */
    public function addLoan(Loan $loan, bool $updateRelation = true): self
    {
        if ($updateRelation || !$loan->getBook()) {
            $loan->setBook($this, false);
        }

        if ($loan->getBook() !== $this) {
            throw new InvalidArgumentException('A book can be added to its loan list only if he is the book in the Loan object');
        }

        if ($this->loans->contains($loan)) {
            return $this;
        }

        // @todo check if the book of the owner is available or already borrowed by someone: throw an exception to explain that it must be returned before it can be loaned again

        $this->loans->add($loan);

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
        return $this->getTitle()
            . ($this->getDescription() ? ', ' . $this->getDescription() : '')
            . (!is_null($this->getIndexInSerie()) ? ', #' . $this->getIndexInSerie() : '')
            ;
    }
}
