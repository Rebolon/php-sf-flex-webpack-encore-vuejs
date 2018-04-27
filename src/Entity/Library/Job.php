<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     iri="http://schema.org/Role",
 *     attributes={"access_control"="is_granted('ROLE_USER')"}
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
     */
    private $id;

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
     */
    private $translationKey;

    /**
     * id can be null until flush is done
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Job
     */
    public function setId($id): Job
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTranslationKey(): ?string
    {
        return $this->translationKey;
    }

    /**
     * @param mixed $translationKey
     * @return Job
     */
    public function setTranslationKey($translationKey): Job
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
        return $this->getTranslationKey();
    }
}
