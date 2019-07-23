<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo would be interesting to add cache here (seems to need a varnish server)
 *     cacheHeaders={"max_age"=3600, "shared_max_age"=7200}
 *
 * @ApiResource(
 *     iri="http://schema.org/Role",
 *     attributes={
 *          "access_control"="is_granted('ROLE_USER')",
 *          "pagination_client_enabled"=true
 *      },
 *     collectionOperations={
 *          "get"={"method"="GET"}
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"}
 *     }
 * )
 *
 * @ORM\Entity
 */
class Job implements LibraryInterface
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
     * @ApiProperty(
     *     iri="http://schema.org/name"
     * )
     * @Groups({"book_detail_read", "book_detail_write"})
     *
     * @ORM\Column(type="string", length=256, nullable=false, name="translation_key")
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="256")
     *
     * @var string
     */
    protected $translationKey;

    /**
     * Job constructor.
     */
    public function __construct()
    {
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
    public function getTranslationKey(): ?string
    {
        return $this->translationKey;
    }

    /**
     * @param mixed $translationKey
     * @return self
     */
    public function setTranslationKey($translationKey): self
    {
        $this->translationKey = $translationKey;

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
        return (string) $this->getTranslationKey();
    }
}
