<?php
namespace App\Entity\Project;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An edition company.
 *
 * @ApiResource
 * @ORM\Entity
 */
class Editor
{
    /**
     * @var int The id of this editor.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string The company name.
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string The books of this editor.
     *
     * @ORM\Column
     * @Assert\NotBlank
     * @ORM\OneToMany(targetEntity="Book", mappedBy="editor")
     */
    private $books;
}