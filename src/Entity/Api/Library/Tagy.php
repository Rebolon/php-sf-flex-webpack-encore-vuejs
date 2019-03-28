<?php
namespace App\Entity\Api\Library;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Entity\Library\LibraryInterface;
use Doctrine\Common\Collections\ArrayCollection;
// use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource(
 *     iri="http://schema.org/Tags",
 *     attributes={
 *          "normalization_context"={
 *              "groups"={"book_detail_read"},
 *              "enable_max_depth"=true
 *          },
 *          "denormalization_context"={
 *              "groups"={"book_detail_write"}
 *          }
 *     }
 * )
 * @ ApiFilter(OrderFilter::class, properties={"id", "name"}, arguments={"orderParameterName"="order"})
 * @ ApiFilter(SearchFilter::class, properties={"id": "exact", "name": "istart"})
 * @ ApiFilter(PropertyFilter::class, arguments={"parameterName": "properties", "overrideDefaultProperties": false}))
 */
class Tagy implements LibraryInterface
{
    /**
     * @Groups("book_detail_read")
     */
    protected $id;

    /**
     * @ApiProperty()
     * @Groups({"book_detail_read", "book_detail_write"})
     */
    protected $name;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
    }

    /**
     * id can be null until flush is done
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return self
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }
}
