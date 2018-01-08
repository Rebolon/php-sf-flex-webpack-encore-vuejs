<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ApiResource(
 *     iri="http://schema.org/Series",
 *     attributes={"access_control"="is_granted('ROLE_USER')"}
 * )
 * @ORM\Entity
 */
class Serie
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ApiProperty(
     *      iri="http://pending.schema.org/headline"
     * )
     * @ORM\Column(type="string", length=512, nullable=false)
     */
    private $name;

    /**
     * @ApiProperty(
     *      iri="http://pending.schema.org/ComicStory"
     * )
     * @ApiSubresource(maxDepth=1)
     * @ORM\OneToMany(targetEntity="App\Entity\Library\Book", mappedBy="serie", orphanRemoval=true)
     */
    private $books;

    /**
     * Serie constructor.
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Serie
     */
    public function setName($name): Serie
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return PersistentCollection
     */
    public function getBooks(): PersistentCollection
    {
        return $this->books;
    }
}
