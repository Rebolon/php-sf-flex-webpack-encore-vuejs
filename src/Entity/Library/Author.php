<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
    private $projectBookCreation;

    /**
     * Author constructor.
     */
    public function __construct()
    {
        $this->projectBookCreation = new ArrayCollection();
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
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     * @return Author
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     * @return Author
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProjectBookCreation()
    {
        return $this->projectBookCreation;
    }

    /**
     * @todo the content of the methods + the route mapping for the api
     * Return the list of Books for all projects book creation of this author
     *
     * @return collection|ProjectBookCreation
     */
    public function getBooks()
    {
        // list ProjectBookCreation with fields id/role/book (author is omitted)
    }
}
