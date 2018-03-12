<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     iri="http://schema.org/publisher",
 *     attributes={"access_control"="is_granted('ROLE_USER')"}
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "name"}, arguments={"orderParameterName"="order"})
 * @ORM\Entity
 */
class Editor implements LibraryInterface
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
     * @Assert\NotBlank()
     * @Assert\Length(max="512")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookEdition", mappedBy="editor")
     */
    private $books;

    /**
     * Editor constructor.
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
    public function getName(): ?string
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
     * @return Collection
     */
    public function getBooks(): Collection
    {
        // list ProjectBookEdition with fields id/publicationdate/collection/isbn/book (editor should be omitted to prevent circular reference)
        return $this->books;
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
