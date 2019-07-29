<?php
namespace App\Entity\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use App\Entity\LoggerTrait;
use Doctrine\ORM\Mapping as ORM;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use \DateTime;

/**
 * @ApiResource(
 *     iri="http://schema.org/Review",
 *     attributes={
 *          "access_control"="is_granted('ROLE_USER')",
 *          "pagination_client_enabled"=true
 *      }
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "rating", "username", "publicationDate", "book"})
 * @ApiFilter(DateFilter::class, properties={"publication_date"})
 *
 * @ORM\Entity
 */
class Review implements LibraryInterface
{
    use LoggerTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Assert\Uuid()
     *
     * @var int
     */
    protected $id;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/reviewRating"
     * )
     *
     * @ORM\Column(type="integer", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Range(min="0", max="5")
     *
     * @var int
     */
    protected $rating;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/reviewBody"
     * )
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $body;

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
     *
     * @var string
     */
    protected $username;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/datePublished"
     * )
     *
     * @ORM\Column(type="datetime", nullable=false, name="publication_date")
     *
     * @Assert\NotBlank()
     * @Assert\DateTime()
     *
     * @var DateTime
     */
    protected $publicationDate;

    /**
     * @ApiProperty(
     *     iri="http://bib.schema.org/ComicStory"
     * )
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Library\Book", inversedBy="reviews")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var Book
     */
    protected $book;

    /**
     * ProjectBookEdition constructor.
     *
     * @param LoggerInterface $logger
     * @throws \Exception
     */
    public function __construct(?LoggerInterface $logger)
    {
        if ($logger) {
            $this->setLogger($logger);
        }

        // default value coz since mid-2018 (don't have exact DBAL version) there is no more default attributes for datetime
        $this->setPublicationDate(new DateTime());
    }

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
     * @return DateTime|null
     */
    public function getPublicationDate(): ?DateTime
    {
        return $this->publicationDate;
    }

    /**
     * @param DateTime|string $publicationDate
     * @return self
     */
    public function setPublicationDate($publicationDate): self
    {
        // @todo mutualize this code
        if (is_string($publicationDate)) {
            $dateString = $publicationDate;
            try {
                if (preg_match('/\d*/', $publicationDate)) {
                    $dateTime = new DateTime();
                    $publicationDate = $dateTime->setTimestamp((int) $publicationDate);
                } else {
                    $publicationDate = new DateTime($publicationDate);
                }
            } catch (\Exception $e) {
                $this->logger->warning(sprintf('Wrong input for publicationDate, %s', $dateString));
            }
        } elseif (!($publicationDate instanceof DateTime)) {
            $this->logger->warning(sprintf(
                'Wrong input for publicationDate, should be \DateTime or valid date string or unixTimestamp, %s',
                is_object($publicationDate) ? $publicationDate->__toString() : $publicationDate
            ));
        }

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
