<?php


namespace App\OAuth\Repository\Pdo\Factory;

use App\OAuth\Repository\Pdo\Scope;
use PDO;
use Psr\Container\ContainerInterface;

final class ScopeFactory
{
    /**
     * @param ContainerInterface $container
     * @return Scope
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var PDO $pdo */
        $pdo = $container->get(PDO::class);

        return new Scope($pdo);
    }
}
