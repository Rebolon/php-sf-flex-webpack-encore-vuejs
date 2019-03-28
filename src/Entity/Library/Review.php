<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     iri="http://schema.org/Review",
 *     attributes={"access_control"="is_granted('ROLE_USER')"}
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "rating", "username", "publicationDate", "book"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(DateFilter::class, properties={"publication_date"})
 *
 * @ORM\Entity
 */
class Review implements LibraryInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Assert\Uuid()
     */
    private $id;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/reviewRating"
     * )
     *
     * @ORM\Column(type="integer", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Range(min="0", max="5")
     */
    private $rating;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/reviewBody"
     * )
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;

    /**
     * @todo change username by user and map ManyToOne on Reader => only user that has read the book can set a review ;-)
     *
     * @ApiProperty(
     *     iri="http://schema.org/givenName"
     * )
     *
     * @ORM\Column(type="string", length=512, nullable=true)
     *
     * @Assert\Length(max="512")
     */
    private $username;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/datePublished"
     * )
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default":"now()"}, name="publication_date")
     *
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $publicationDate;

    /**
     * @ApiProperty(
     *     iri="http://bib.schema.org/ComicStory"
     * )
     * @ApiSubresource(maxDepth=1)
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Library\Book", inversedBy="reviews")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $book;

    /**
     * id can be null until flush is done
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     * @return self
     */
    public function setRating($rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     * @return self
     */
    public function setBody($body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return self
     */
    public function setUsername($username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getPublicationDate(): ?\DateTime
    {
        return $this->publicationDate;
    }

    /**
     * @param mixed $publicationDate
     * @return self
     */
    public function setPublicationDate($publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * @return Book|null
     */
    public function getBook(): ?Book
    {
        return $this->book;
    }

    /**
     * Mandatory for EasyAdminBundle (if i don't want to do custom dev)
     *
     * @param Book $book
     * @return self
     */
    public function setBook(Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Mandatory for EasyAdminBundle to build the select box
     * It also helps to build a footprint of the object, even if with the Serializer component it might be more pertinent
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getRating()
            . ($this->getBody() ? ': ' . $this->getBody() : '')
            . ($this->getUsername() ? ', @' . $this->getUsername() : '')
            . ($this->getPublicationDate() ? ' (' . $this->getPublicationDate()->format('Ymd') . ')' : '');
    }
}
