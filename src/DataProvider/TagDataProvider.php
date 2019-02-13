<?php
namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Library\Tag;
use App\Entity\Ping;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class TagDataProvider implements ItemDataProviderInterface, CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $apiPlatformConfig = [];

    public function __construct(ManagerRegistry $managerRegistry, LoggerInterface $logger)
    {
        $this->managerRegistry = $managerRegistry;
        $this->apiPlatformConfig = [
            'collection' => [
                'order_parameter_name' => 'order',
                'pagination' => [
                    'items_per_page' => 30,
                    'items_per_page_parameter_name' => 'itemsPerPage',
                    'maximum_items_per_page' => 50,
                    'page_parameter_name' => 'page',
                ]
            ]
        ];

        try {
            $apiPlatformConfigFile = __DIR__ . '/../../config/packages/api_platform.yaml';
            if (is_file($apiPlatformConfigFile)) {
                $missingKeys = [];
                $config = Yaml::parseFile($apiPlatformConfigFile);
                foreach ($this->apiPlatformConfig['collection']['pagination'] as $key => $value) {
                    if (array_key_exists($key, $config['api_platform']['collection']['pagination'])) {
                        $this->apiPlatformConfig['collection']['pagination'][$key] = $config['api_platform']['collection']['pagination'][$key];
                        continue;
                    }

                    $missingKeys[] = $key;
                }

                $key = 'order_parameter_name';
                if (!array_key_exists($key, $config['api_platform']['collection'])) {
                    $missingKeys[] = $key;
                } else {
                    $this->apiPlatformConfig['collection'][$key] = $config['api_platform']['collection'][$key];
                }

                if ($missingKeys) {
                    throw new \RuntimeException(sprintf('Missing keys in api_platform config file with pagination node, (%s)', join(', ', $missingKeys)));
                }
            } else {
                throw new \RuntimeException('Missing api_platform config file with pagination node');
            }
        } catch (\Exception $e) {
            $logger->warning($e->getMessage());
        }
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
            // @todo itemsPerPage is defined in symfony parameters => should be retreived by DI
            $orderBy = array_key_exists($this->apiPlatformConfig['collection']['order_parameter_name'], $filters) ? $filters['order'] : null;
            $limit = array_key_exists($this->apiPlatformConfig['collection']['pagination']['items_per_page_parameter_name'], $filters) ? $filters[$this->apiPlatformConfig['collection']['pagination']['items_per_page_parameter_name']] : null;
            $offset = array_key_exists($this->apiPlatformConfig['collection']['pagination']['page_parameter_name'], $filters) ? $filters['page'] : null;
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
