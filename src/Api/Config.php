<?php
namespace App\Api;

use App\Kernel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class Config
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $apiPlatformConfig = [];

    public function __construct(string $kernelProjectDir, LoggerInterface $logger)
    {
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
            $apiPlatformConfigFile = $kernelProjectDir . '/config/packages/api_platform.yaml';
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

    /**
     * @return array
     */
    public function getApiPlatformConfig(): array
    {
        return $this->apiPlatformConfig;
    }

    /**
     * @return string
     */
    public function getNameParameterOrder(): string
    {
        return $this->apiPlatformConfig['collection']['order_parameter_name'];
    }

    /**
     * @return string
     */
    public function getNameParameterPaginationPage(): string
    {
        return $this->apiPlatformConfig['collection']['pagination']['page_parameter_name'];
    }

    /**
     * @param null $resourceAnnotation
     * @return bool
     */
    public function hasPaginationEnabled($resourceAnnotation = null): bool
    {
        $enabledGlobally = isset($this->apiPlatformConfig['collection']['pagination']['enabled']) ? $this->apiPlatformConfig['collection']['pagination']['enabled'] : true;

        $enabledLocally = isset($resourceAnnotation->attributes)
            && array_key_exists('pagination_enabled', $resourceAnnotation->attributes) ?
            $resourceAnnotation->attributes['pagination_enabled'] : true;

        return $enabledGlobally && $enabledLocally;
    }

    /**
     * @return string
     */
    public function getNameParameterPaginationItemsPerPage(): string
    {
        return $this->apiPlatformConfig['collection']['pagination']['items_per_page_parameter_name'];
    }

    /**
     * @return string
     */
    public function getPaginationItemsPerPage(): string
    {
        return $this->apiPlatformConfig['collection']['pagination']['items_per_page'];
    }

    /**
     * @return string
     */
    public function getPaginationMaxItemsPerPage(): string
    {
        return $this->apiPlatformConfig['collection']['pagination']['maximum_items_per_page'];
    }
}
