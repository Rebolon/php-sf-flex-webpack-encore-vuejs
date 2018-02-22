<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     iri="http://schema.org/author",
 *     attributes={"access_control"="is_granted('ROLE_USER')", "status_code"=403}
 * )
 * @ORM\Entity
 */
class Author implements LibraryInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ApiProperty (
     *     iri="http://schema.org/givenName"
     * )
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @ApiProperty (
     *     iri="http://schema.org/familyName"
     * )
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastname;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookCreation", mappedBy="author")
     */
    private $books;

    /**
     * Author constructor.
     */
    public function __construct()
    {
        $this->books = new ArrayCollection();
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
     * @return string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     * @return Author
     */
    public function setFirstname($firstname): Author
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     * @return Author
     */
    public function setLastname($lastname): Author
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
     * @return string
     */
    public function getName(): ?string
    {
        return trim($this->getFirstname() . ' ' . $this->getLastname());
    }

    /**
     * Mandatory for EasyAdminBundle to build the select box
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }
}
