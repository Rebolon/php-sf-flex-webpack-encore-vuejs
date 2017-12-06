<?php
namespace App\Entity\Project;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A book.
 *
 * @ApiResource
 * @ORM\Entity
 */
class Author
{
    /**
     * @var int The id of this author.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string The firstname of this author.
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    private $firstname;

    /**
     * @var string The lastname of this author.
     *
     * @ORM\Column
     */
    private $lastname;

    /**
     * @var string Books created by this author.
     *
     * @ORM\Column
     * @ORM\OneToMany(targetEntity="Project", mappedBy="author")
     */
    private $books;
}