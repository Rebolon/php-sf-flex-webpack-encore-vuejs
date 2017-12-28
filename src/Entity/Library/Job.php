<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(iri="http://schema.org/Role")
 * @ORM\Entity
 */
class Job
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/name"
     * )
     * @ORM\Column(type="string", length=256, nullable=false)
     */
    private $tanslation_key;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/roleName"
     * )
     * @ORM\Column(type="integer", nullable=true, options={"unsigned":true})
     */
    private $role;

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
    public function getTanslationKey(): string
    {
        return $this->tanslation_key;
    }

    /**
     * @param mixed $tanslation_key
     * @return Job
     */
    public function setTanslationKey($tanslation_key): Job
    {
        $this->tanslation_key = $tanslation_key;

        return $this;
    }

    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     * @return Job
     */
    public function setRole($role): Job
    {
        $this->role = $role;

        return $this;
    }
}
