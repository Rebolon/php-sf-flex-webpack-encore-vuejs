<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ApiResource(iri="http://schema.org/author")
 * @ORM\Entity
 */
class Author
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
    public function getFirstname(): string
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
     * @return PersistentCollection
     */
    public function getBooks(): PersistentCollection
    {
        // list ProjectBookCreation with fields id/role/book (author should be omitted to prevent circular reference)
        return $this->books;
    }
}
