<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     iri="http://schema.org/Series",
 *     security="is_granted('ROLE_USER')",
 *     paginationClientEnabled=true
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "name"})
 * @ApiFilter(SearchFilter::class, properties={"name": "istart"})
 * @ApiFilter(PropertyFilter::class, arguments={"parameterName": "properties", "overrideDefaultProperties": false}))
 *
 * @ORM\Entity
 */
class Serie implements LibraryInterface
{
    /**
     * @Groups("book:detail:read")
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
     *      iri="http://pending.schema.org/headline"
     * )
     * @Groups({"book:detail:read", "book:detail:write"})
     *
     * @ORM\Column(type="string", length=512, nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="512")
     */
    protected string $name;

    /**
     * @ApiProperty(
     *      iri="http://pending.schema.org/ComicStory"
     * )
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Library\Book", mappedBy="serie", orphanRemoval=true)
     */
    protected Collection $books;

    /**
     * Serie constructor.
     */
    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    /**
     * id can be null until flush is done
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return self
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @param Book $book
     * @param bool $updateRelation
     * @return $this
     */
    public function addBook(Book $book, bool $updateRelation = true): self
    {
        if ($this->books->contains($book)) {
            return $this;
        }

        if ($updateRelation) {
            $book->setSerie($this, false);
        }

        $this->books[] = $book;

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
        return (string) $this->getName();
    }
}
