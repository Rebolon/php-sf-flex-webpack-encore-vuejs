<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo sort based on the couple firstname + lastname instead of just each fields independently
 *
 * @ApiResource(
 *     iri="http://schema.org/author",
 *     attributes={
 *          "access_control"="is_granted('ROLE_USER')",
 *          "status_code"=403,
 *          "pagination_client_enabled"=true,
 *     }
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "lastname", "firstname"})
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "firstname": "istart", "lastname": "istart"})
 * @ApiFilter(PropertyFilter::class, arguments={"parameterName": "properties", "overrideDefaultProperties": false}))
 *
 * @ORM\Entity
 */
class Author implements LibraryInterface
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
     * @ApiProperty (
     *     iri="http://schema.org/givenName"
     * )
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\Column(type="string", nullable=false)
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $firstname;

    /**
     * @ApiProperty (
     *     iri="http://schema.org/familyName"
     * )
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $lastname;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookCreation", mappedBy="author")
     *
     * @var Collection|ProjectBookCreation[]
     */
    protected $books;

    /**
     * Author constructor.
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
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     * @return self
     */
    public function setFirstname($firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     * @return self
     */
    public function setLastname($lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @todo the content of the methods + the route mapping for the api
     * Return the list of Books for all projects book creation of this author
     *
     * @return Collection
     */
    public function getBooks(): Collection
    {
        // list ProjectBookCreation with fields id/role/book (author should be omitted to prevent circular reference)
        return $this->books;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return trim($this->getFirstname() . ' ' . $this->getLastname());
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
