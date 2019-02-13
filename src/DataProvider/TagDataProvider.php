<?php
namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Api\Config;
use App\Entity\Library\Tag;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

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

    public function __construct(ManagerRegistry $managerRegistry, Config $apiPlatformConfig)
    {
        $this->managerRegistry = $managerRegistry;
        $this->apiPlatformConfig = $apiPlatformConfig;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Tag::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $em = $this->managerRegistry->getRepository(Tag::class);
        $tag = $em->find($id);

        return $tag;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $em = $this->managerRegistry->getRepository(Tag::class);

        // useless if ApiResource has no pagination
        $filters = array_key_exists('filters', $context) ? $context['filters'] : null;
        if ($filters) {
            $orderKey = $this->apiPlatformConfig->getNameParameterOrder();
            $limitKey = $this->apiPlatformConfig->getNameParameterPaginationItemsPerPage();
            $pageKey = $this->apiPlatformConfig->getNameParameterPaginationPage();

            $orderBy = array_key_exists($orderKey, $filters) ? $filters[$orderKey] : null;
            $limit = array_key_exists($limitKey, $filters) ? $filters[$limitKey] : null;
            $offset = array_key_exists($pageKey, $filters) ? $filters[$pageKey] : null;
            $resTag = $em->findBy([], $orderBy, $limit, $offset);
        } else {
            $resTag = $em->findAll();
        }

        $tags = [];

        foreach ($resTag as $row) {
            $tag = new Tag();
            $tag->setId($row->getId())
                ->setName($row->getName());

            $tags[] = $tag;
        }

        return $tags;
    }

}
