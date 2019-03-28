<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Ping
 * @ApiResource(
 *     attributes={"access_control"="is_granted('ROLE_USER')", "status_code"=403},
 *     itemOperations={
 *         "get"={"method"="GET"}
 *     },
 *     collectionOperations={
 *          "get"={"method"="GET"}
 *     }
 * )
 * @package App\Entity\PingSecured
 */
class PingSecured
{
    /**
     * @Assert\Uuid()
     * @ApiProperty(identifier=true)
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $pong = 'pong';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getPong()
    {
        return $this->pong;
    }
}
