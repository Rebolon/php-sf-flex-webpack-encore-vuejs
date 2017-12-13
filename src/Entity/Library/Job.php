<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource
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
     * @ORM\Column(type="string", length=256, nullable=false)
     */
    private $tanslation_key;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned":true})
     */
    private $role;

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
    public function getTanslationKey()
    {
        return $this->tanslation_key;
    }

    /**
     * @param mixed $tanslation_key
     * @return Job
     */
    public function setTanslationKey($tanslation_key)
    {
        $this->tanslation_key = $tanslation_key;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     * @return Job
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }
}
