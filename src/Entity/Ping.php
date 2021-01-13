<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Ping
 * @ApiResource(
 *     itemOperations={
 *         "get"
 *     },
 *     collectionOperations={
 *          "get"
 *     }
 * )
 * @package App\Entity\Ping
 */
class Ping
{
    /**
     * @ApiProperty(identifier=true)
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $pong = 'ping';

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
