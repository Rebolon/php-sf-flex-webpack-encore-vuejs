<?php
namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ItemNotFoundException;
use App\Entity\Ping;
use App\Entity\PingSecured;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class PingDataProvider used for Ping and PingSecured
 *
 * @package App\DataProvider
 */
class PingDataProvider implements ItemDataProviderInterface, CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var ArrayCollection
     */
    protected $fixtures;

    public function __construct()
    {
        $this->fixtures = new ArrayCollection();

        for ($i=0 ; $i<10 ; $i++) {
            $pong = new Ping();
            $pong->setId($i);
            $this->fixtures->add($pong);
        }
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Ping::class === $resourceClass || PingSecured::class === $resourceClass ;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        foreach ($this->fixtures as $pong) {
            if ($pong->getId() === $id) {
                return $pong;
            }
        }

        throw new ItemNotFoundException();
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        return $this->fixtures;
    }
}
