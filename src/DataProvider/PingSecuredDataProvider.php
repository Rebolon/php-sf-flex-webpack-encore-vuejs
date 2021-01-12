<?php
namespace App\DataProvider;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Security\ResourceAccessCheckerInterface;
use App\Entity\PingSecured;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Persistence\ManagerRegistry;
use function get_class;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class PingSecuredDataProvider implements ItemDataProviderInterface, CollectionDataProviderInterface, RestrictedDataProviderInterface
{

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var ResourceAccessCheckerInterface
     */
    protected $resourceAccessChecker;

    /**
     * @var JWTTokenManagerInterface
     */
    protected $jwtManager;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var array|false
     */
    protected $tokenInfo;

    /**
     * ByUserDataProvider constructor.
     * @param ManagerRegistry $managerRegistry
     * @param TokenStorageInterface $tokenStorage
     * @param JWTTokenManagerInterface $jwtManager
     * @param ResourceAccessCheckerInterface $resourceAccessChecker
     */
    public function __construct(ManagerRegistry $managerRegistry, TokenStorageInterface $tokenStorage, JWTTokenManagerInterface $jwtManager, ResourceAccessCheckerInterface $resourceAccessChecker)
    {
        $this->managerRegistry = $managerRegistry;
        $this->resourceAccessChecker = $resourceAccessChecker;
        $this->jwtManager = $jwtManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array $context
     * @return bool
     * @throws ReflectionException
     * @throws AnnotationException
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        $supports = PingSecured::class === $resourceClass;

        if (!$supports) {
            return $supports;
        }

        // In DataProvider/DataPersister security is no more managed by ApiPlatform and access_control annotations, so we have to managed it manually and with api_platform.yaml config
        try {
            $this->tokenInfo = $this->jwtManager->decode($this->tokenStorage->getToken());

            $annotationReader = new AnnotationReader();
            $reflectionClass = new ReflectionClass($resourceClass);
            foreach ($annotationReader->getClassAnnotations($reflectionClass) as $classAnnotation) {
                if (ApiResource::class === get_class($classAnnotation)) {
                    $resourceAnnotation = $classAnnotation;
                    break;
                }
            }

            if (!isset($resourceAnnotation)) {
                throw new RuntimeException(
                    'An API resource must be defined with Annotation @ApiResource (yaml is not managed here)'
                );
            }

            // Check By operation getItem / getCollection
            if ($operationName
                && count($context)) {
                $prop = $context['operation_type'].'Operations';
                if (!property_exists($resourceAnnotation, $prop)) {
                    throw new RuntimeException(sprintf("Unknown operationType %s", $context['operation_type']));
                }

                $contextAccessControl = array_key_exists($operationName, $resourceAnnotation->$prop) && $resourceAnnotation->$prop[$operationName]['access_control'] ? $resourceAnnotation->$prop[$operationName]['access_control'] : null;
                if ($contextAccessControl
                    && !$this->resourceAccessChecker->isGranted(PingSecured::class, $resourceAnnotation->attributes["access_control"])) {
                    throw new AuthenticationException('User is not granted to access this feature');
                }
            }

            // Check Main accessControl (or the one in attributes)
            $mainAccessControl = $resourceAnnotation->attributes["access_control"];
            if ($mainAccessControl
                && !$this->resourceAccessChecker->isGranted(PingSecured::class, $resourceAnnotation->attributes["access_control"])) {
                throw new AuthenticationException('User is not granted to access this feature');
            }
        } catch (AuthenticationException $e) {
            throw $e;
        } catch (ReflectionException $e) {
            throw $e;
        }

        return $supports;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $pong = new PingSecured();
        $pong->setId(1);

        return $pong;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $pongs = [];
        for ($i = 0; $i < 3; $i++) {
            $ping = new PingSecured();
            $ping->setId($i);

            $pongs[] = $ping;
        }

        return $pongs;
    }
}
