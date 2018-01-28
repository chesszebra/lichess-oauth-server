<?php

namespace App\OAuth\Repository\Pdo\Factory;

use App\OAuth\Repository\Pdo\RefreshToken;
use PDO;
use Psr\Container\ContainerInterface;

final class RefreshTokenFactory
{
    /**
     * @param ContainerInterface $container
     * @return RefreshToken
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var PDO $pdo */
        $pdo = $container->get(PDO::class);

        return new RefreshToken($pdo);
    }
}
