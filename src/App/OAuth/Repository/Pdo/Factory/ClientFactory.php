<?php

namespace App\OAuth\Repository\Pdo\Factory;

use App\OAuth\Repository\Pdo\Client;
use PDO;
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
        /** @var PDO $pdo */
        $pdo = $container->get(PDO::class);

        return new Client($pdo);
    }
}
