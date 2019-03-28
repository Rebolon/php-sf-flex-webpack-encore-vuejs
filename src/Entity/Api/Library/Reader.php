<?php
namespace App\Entity\Api\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Entity\Library\LibraryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     iri="http://bib.schema.org/user",
 *     collectionOperations={
 *          "get"={"method"="GET"},
 *          "post"={"method"="POST", "access_control"="is_granted('ROLE_ADMIN')", "access_control_message"="Only admin users can add users."}
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *         "put"={"method"="PUT", "access_control"="is_granted('ROLE_ADMIN')", "access_control_message"="Only admin users can modify users."},
 *         "delete"={"method"="delete", "access_control"="is_granted('ROLE_ADMIN')", "access_control_message"="Only admin users can delete users."}
 *     },
 *     attributes={
 *          "normalization_context"={
 *              "groups"={"reader_read", "loan_read"}
 *          },
 *          "denormalization_context"={
 *              "groups"={"reader_write", "loan_read"}
 *          }
 *     }
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "lastname"}, arguments={"orderParameterName"="order"})
 */
class Reader implements LibraryInterface
{
    /**
     * @ApiProperty(
     *     iri="http://schema.org/identifier"
     * )
     * @Groups({"reader_read"})
     */
    private $id;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/lastname"
     * )
     * @Groups({"reader_read", "reader_write"})
     *
     */
    private $lastname;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/description"
     * )
     * @Groups({"reader_read", "reader_write"})
     */
    private $firstname;

    /**
     * @todo it may not be a list of books but a list of projectEdition coz you may get a book more than once but in
     * different edition ! For instance i keep this implementation for the sample but i might improve this in future
     *
     * @ApiProperty()
     * @Groups({"reader_read", "reader_write"})
     */
    private $myLibrary;

    /**
     * id can be null until flush is done
     *
     * @return int
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
     * @return mixed
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     * @return self
     */
    public function setLastname($lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     * @return self
     */
    public function setFirstname($firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMyLibrary(): Collection
    {
        return $this->myLibrary;
    }

    /**
     * @param ArrayCollection $aLibrary
     * @return self
     */
    public function setTags(ArrayCollection $aLibrary): self
    {
        $this->myLibrary = $aLibrary;

        return $this;
    }

    /**
     * @param Book $book
     * @return self
     */
    public function addMyLibrary(Book $book): self
    {
        if ($this->hasBookInMyLibrary($book)) {
            return $this;
        }

        $this->myLibrary[] = $book;

        return $this;
    }

    /**
     * @param Book $book
     * @return bool
     */
    protected function hasBookInMyLibrary(Book $book): bool
    {
        foreach ($this->myLibrary as $bookIAlreadyGet) {
            if (
                (
                    (!is_null($book->getId())
                    && $book->getId() === $bookIAlreadyGet->getId())
                ) || (
                    (is_null($book->getId())
                    && $book->getTitle() === $bookIAlreadyGet->getTitle())
                )
            ) {
                return true;
            }
        }

        return false;
    }
}
