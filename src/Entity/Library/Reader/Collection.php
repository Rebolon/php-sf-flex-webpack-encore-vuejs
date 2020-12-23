<?php

namespace App\Entity\Library\Reader;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\Library\Reader\CollectionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=CollectionRepository::class)
 */
class Collection
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
