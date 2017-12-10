<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="project_book_edition")
 */
class ProjectBookEdition
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true, options={"default":"now()"})
     */
    private $publication_date;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $collection;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Editor",
     *     inversedBy="projectBookEdition",
     *     fetch="EAGER"
     * )
     * @ORM\JoinColumn(name="editor_id", referencedColumnName="id")
     */
    private $editor;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Library\Book",
     *     inversedBy="projectBookEdition",
     *     fetch="EAGER"
     * )
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     */
    private $book;

    /**
     * @return mixed
     */
    public function getPublicationDate()
    {
        return $this->publication_date;
    }

    /**
     * @param mixed $publication_date
     * @return ProjectBookEdition
     */
    public function setPublicationDate($publication_date)
    {
        $this->publication_date = $publication_date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param mixed $collection
     * @return ProjectBookEdition
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * @param Editor $editor
     * @return $this
     */
    public function setAuthor(Editor $editor)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBook()
    {
        return $this->book;
    }
}