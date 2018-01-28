<?php

namespace App\Factory;

use PDO;
use Psr\Container\ContainerInterface;

final class PdoFactory
{
    /**
     * @param ContainerInterface $container
     * @return PDO
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var array $config */
        $config = $container->get('config');

        return new PDO(
            $config['pdo']['dsn'],
            $config['pdo']['username'],
            $config['pdo']['password'],
            $config['pdo']['options']
        );
    }
}
