<?php

namespace App\OAuth\Repository\Pdo\Factory;

use App\OAuth\Repository\Pdo\AuthCode;
use PDO;
use Psr\Container\ContainerInterface;

final class AuthCodeFactory
{
    /**
     * @param ContainerInterface $container
     * @return AuthCode
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var PDO $pdo */
        $pdo = $container->get(PDO::class);

        return new AuthCode($pdo);
    }
}
