<?php

namespace App\OAuth\Repository\Pdo\Factory;

use App\OAuth\Repository\Pdo\AccessToken;
use PDO;
use Psr\Container\ContainerInterface;

final class AccessTokenFactory
{
    /**
     * @param ContainerInterface $container
     * @return AccessToken
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var PDO $pdo */
        $pdo = $container->get(PDO::class);

        return new AccessToken($pdo);
    }
}
