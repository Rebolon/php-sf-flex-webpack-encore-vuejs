<?php
namespace App\Entity\Project;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A Serie of books.
 *
 * @ApiResource
 * @ORM\Entity
 */
class Serie
{
    /**
     * @var int The id of this serie.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string The serie name.
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string The books of this serie.
     *
     * @ORM\Column
     * @Assert\NotBlank
     * @ORM\OneToMany(targetEntity="Book", mappedBy="serie")
     */
    private $books;
}