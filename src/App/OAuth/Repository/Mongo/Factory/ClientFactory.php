<?php

namespace App\OAuth\Repository\Mongo\Factory;

use App\OAuth\Repository\Mongo\Client;
use MongoDB\Client as MongoClient;
use MongoDB\Collection;
use MongoDB\Database;
use Psr\Container\ContainerInterface;

final class ClientFactory
{
    /**
     * @param ContainerInterface $container
     * @return Client
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var MongoClient $client */
        $client = $container->get(MongoClient::class);

        /** @var Database $database */
        $database = $client->selectDatabase('lichess');

        /** @var Collection $collection */
        $collection = $database->selectCollection('oauth_client');

        return new Client($collection);
    }
}
