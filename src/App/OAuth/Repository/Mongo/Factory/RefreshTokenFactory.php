<?php

namespace App\OAuth\Repository\Mongo\Factory;

use App\OAuth\Repository\Mongo\RefreshToken;
use MongoDB\Client as MongoClient;
use MongoDB\Collection;
use MongoDB\Database;
use Psr\Container\ContainerInterface;

final class RefreshTokenFactory
{
    /**
     * @param ContainerInterface $container
     * @return RefreshToken
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
        $collection = $database->selectCollection('oauth_refresh_token');

        return new RefreshToken($collection);
    }
}
