<?php
namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Ping;

class PingDataProvider implements ItemDataProviderInterface, CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Ping::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $pong = new Ping();

        return $pong;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $pong = new Ping();

        return [$pong];
    }

}
