<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(iri="http://schema.org/publisher")
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
     * @ApiProperty(
     *     iri="http://schema.org/legalName"
     * )
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Editor
     */
    public function setName($name): Editor
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @todo the content of the methods + the route mapping for the api
     * Return the list of Books for all projects book edition of this editor
     *
     * @return ArrayCollection
     */
    public function getBooks(): ArrayCollection
    {
        // list ProjectBookEdition with fields id/publicationdate/collection/isbn/book (editor should be omitted to prevent circular reference)
        return $this->projectBookEdition;
    }
}
