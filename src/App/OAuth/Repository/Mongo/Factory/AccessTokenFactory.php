<?php

namespace App\OAuth\Repository\Mongo\Factory;

use App\OAuth\Repository\Mongo\AccessToken;
use MongoDB\Client as MongoClient;
use MongoDB\Collection;
use MongoDB\Database;
use Psr\Container\ContainerInterface;

final class AccessTokenFactory
{
    /**
     * @param ContainerInterface $container
     * @return AccessToken
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var MongoClient $client */
        $client = $container->get(MongoClient::class);

        /** @var Database $database */
        $database = $client->selectDatabase('lichess');

        /** @var Collection $collection */
        $collection = $database->selectCollection('oauth_access_token');

        return new AccessToken($collection);
    }
}
