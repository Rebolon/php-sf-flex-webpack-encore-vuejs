<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource
 * @ORM\Entity
 */
class Editor
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=512, nullable=false)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookEdition", mappedBy="editor")
     */
    private $projectBookEdition;

    /**
     * Editor constructor.
     */
    public function __construct()
    {
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Editor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @todo the content of the methods + the route mapping for the api
     * Return the list of Books for all projects book edition of this editor
     *
     * @return collection|ProjectBookEdition
     */
    public function getBooks()
    {
        // list ProjectBookEdition with fields id/publicationdate/collection/isbn/book (editor is omitted)
        return $this->projectBookEdition;
    }
}
