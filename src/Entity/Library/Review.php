<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Flex\Response;

/**
 * @ApiResource(
 *     iri="http://schema.org/Review",
 *     attributes={"access_control"="is_granted('ROLE_USER')"}
 * )
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
     * @ApiSubresource(maxDepth=1)
     * @ORM\ManyToOne(targetEntity="App\Entity\Library\Book", inversedBy="reviews")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $book;

    /**
     * id can be null until flush is done
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     * @return Review
     */
    public function setRating($rating): Review
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     * @return Review
     */
    public function setBody($body): Review
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return Review
     */
    public function setUsername($username): Review
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublicationDate(): \DateTime
    {
        return $this->publication_date;
    }

    /**
     * @param mixed $publication_date
     * @return Review
     */
    public function setPublicationDate($publication_date): Review
    {
        $this->publication_date = $publication_date;

        return $this;
    }

    /**
     * @return Book
     */
    public function getBook(): Book
    {
        return $this->book;
    }
}
