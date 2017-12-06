<?php
namespace App\Entity\Project;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A project of creation that rely on one book and many authors for a specific job.
 *
 * @ApiResource
 * @ORM\Entity
 */
class Project
{
    /**
     * @var int The id of this creation.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string Author that participate in the creation of the book.
     *
     * @ORM\Column
     * @ORM\ManyToOne(targetEntity="Author", mappedBy="id")
     * @JoinColumn(name="author_id", referencedColumnName="id")

     */
    private $author;

    /**
     * @var string Books created by this author.
     *
     * @ORM\Column
     * @ORM\ManyToOne(targetEntity="Book", mappedBy="id")
     * @JoinColumn(name="book_id", referencedColumnName="id")
     */
    private $book;

    /**
     * @var string The job of this author for this book.
     *
     * @ORM\Column
     * @Assert\NotBlank
     * @Enum({"SCENARISTE", "DESSINATEUR", "COULEUR", "TOUT"})
     */
    private $job;
}