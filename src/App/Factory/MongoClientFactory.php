<?php

namespace App\Factory;

use MongoDB\Client;
use Psr\Container\ContainerInterface;

final class MongoClientFactory
{
    /**
     * @param ContainerInterface $container
     * @return Client
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     * @throws \MongoDB\Driver\Exception\InvalidArgumentException
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var array $config */
        $config = $container->get('config');

        return new Client(
            $config['mongodb']['uri'],
            $config['mongodb']['uriOptions'],
            $config['mongodb']['driverOptions']
        );
    }
}
