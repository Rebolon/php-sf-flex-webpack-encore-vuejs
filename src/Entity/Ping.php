<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;

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
     * @var string
     */
    protected $pong = 'pong';

    /**
     * @return string
     */
    public function getPong()
    {
        return $this->pong;
    }
}
