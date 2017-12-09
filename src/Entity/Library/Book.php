<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource
 * @ORM\Entity
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\Isbn
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $index_in_serie;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Library\Review", mappedBy="Book")
     * @ApiSubresource
     */
    private $Reviews;

    /**
     * @var ProjectBookCreation
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookCreation", mappedBy="book")
     */
    private $projectBookCreation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Library\ProjectBookEdition", mappedBy="book")
     */
    private $projectBookEdition;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Library\Serie", inversedBy="book")
     * @ORM\JoinColumn(name="serie_id", referencedColumnName="id")
     * @ApiSubresource
     */
    private $serie;

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
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @param mixed $isbn
     * @return Book
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Book
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return Book
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIndexInSerie()
    {
        return $this->index_in_serie;
    }

    /**
     * @param mixed $indexInSerie
     * @return Book
     */
    public function setIndexInSerie($indexInSerie)
    {
        $this->index_in_serie = $indexInSerie;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReviews()
    {
        return $this->Reviews;
    }

    /**
     * @return mixed
     */
    public function getProjectBookCreation()
    {
        return $this->projectBookCreation;
    }

    /**
     * @return mixed
     */
    public function getProjectBookEdition()
    {
        return $this->projectBookEdition;
    }

    /**
     * @return mixed
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * @param Author $author
     * @param int $role
     * @return $this
     */
    public function addAuthor(Author $author, int $role)
    {
        $this->projectBookCreation
            ->setAuthor($author)
            ->setRole($role)
            ->setBook($this);

        return $this;
    }

    /**
     * @param Editor $editor
     * @param \DateTime $date
     * @param null $collection
     * @return $this
     */
    public function addEditor(Editor $editor, \DateTime $date, $collection = null)
    {
        $this->projectBookEdition
            ->setEditor($editor)
            ->setDate($date)
            ->setCollection($collection);

        return $this;
    }
}