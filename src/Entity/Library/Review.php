<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(iri="http://schema.org/Review")
 * @ORM\Entity
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/reviewRating"
     * )
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/reviewBody"
     * )
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/givenName"
     * )
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $username;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/datePublished"
     * )
     * @ORM\Column(type="datetime", nullable=false, options={"default":"now()"})
     */
    private $publication_date;

    /**
     * @ApiProperty(
     *     iri="http://bib.schema.org/ComicStory"
     * )
     * @ORM\ManyToOne(targetEntity="App\Entity\Library\Book", inversedBy="reviews")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id", onDelete="CASCADE")
     * @ApiSubresource(maxDepth=1)
     */
    private $book;

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
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     * @return Review
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     * @return Review
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return Review
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublicationDate()
    {
        return $this->publication_date;
    }

    /**
     * @param mixed $publication_date
     * @return Review
     */
    public function setPublicationDate($publication_date)
    {
        $this->publication_date = $publication_date;

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
