<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Ping
 * @ApiResource(
 *     itemOperations={
 *         "get"={"method"="GET"}
 *     },
 *     collectionOperations={
 *          "get"={"method"="GET"}
 *     }
 * )
 * @package App\Ping
 */
class Ping
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
     * @return Ping
     */
    public function setId(int $id): Ping
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
