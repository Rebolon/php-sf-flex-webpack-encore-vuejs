<?php
namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Api\Config;
use App\Entity\Library\Tag;
use Doctrine\Persistence\ManagerRegistry;

/**
 * This is how i prefer to customize a DataProvider: use the decorated dataProvider to alter the queryBuilder
 *
 */
class TagDataProvider implements ItemDataProviderInterface, CollectionDataProviderInterface, RestrictedDataProviderInterface, DenormalizedIdentifiersAwareItemDataProviderInterface // this interface is mandatory for getItem to work here, but i wonder why. May entity is not complex and ApiPlatform always add has_identifier_converter in context so it make it crash
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var Config
     */
    protected $apiPlatformConfig = [];

    /**
     * @var DenormalizedIdentifiersAwareItemDataProviderInterface
     */
    protected $itemDataProvider;

    /**
     * @var ContextAwareCollectionDataProviderInterface
     */
    protected $collectionDataProvider;

    public function __construct(\Doctrine\Persistence\ManagerRegistry $managerRegistry, Config $apiPlatformConfig, ContextAwareCollectionDataProviderInterface $collectionDataProvider, DenormalizedIdentifiersAwareItemDataProviderInterface $itemDataProvider)
    {
        $this->managerRegistry = $managerRegistry;
        $this->apiPlatformConfig = $apiPlatformConfig;
        $this->itemDataProvider = $itemDataProvider;
        $this->collectionDataProvider = $collectionDataProvider;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Tag::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $item = $this->itemDataProvider->getItem($resourceClass, $id, $operationName, $context);

        return $item;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $items = $this->collectionDataProvider->getCollection($resourceClass, $operationName, $context);

        return $items;
    }
}
