<?php
namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Api\Config;
use App\Entity\Library\Tag;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * It override the default ApiPlatform system from classes that implement ORM and ApiResource annotations
 *
 * Class TagDataProvider
 * @package App\DataProvider
 */
class TagDataProvider implements ItemDataProviderInterface, CollectionDataProviderInterface, RestrictedDataProviderInterface
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
     * @var iterable
     */
    protected $itemExtensions = [];

    /**
     * @var iterable
     */
    protected $collectionExtensions = [];

    public function __construct(ManagerRegistry $managerRegistry, Config $apiPlatformConfig, iterable $itemExtensions, iterable $collectionExtensions)
    {
        $this->managerRegistry = $managerRegistry;
        $this->apiPlatformConfig = $apiPlatformConfig;
        $this->itemExtensions = $itemExtensions;
        $this->collectionExtensions = $collectionExtensions;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Tag::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $em = $this->managerRegistry->getRepository(Tag::class);

        return $em->find($id);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $tags = [];
        $em = $this->managerRegistry->getRepository(Tag::class);
        $qb = $em->createQueryBuilder('t');
        $queryNameGenerator = new QueryNameGenerator();

        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($qb, $queryNameGenerator, $resourceClass, $operationName, $context);
            if ($extension instanceof QueryResultCollectionExtensionInterface
                && $extension->supportsResult($resourceClass, $operationName, $context)) {
                $tags = $extension->getResult($qb, $resourceClass, $operationName, $context);
            }
        }

        return $tags;
    }

}
