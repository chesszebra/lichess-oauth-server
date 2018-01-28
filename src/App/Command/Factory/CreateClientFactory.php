<?php

namespace App\Command\Factory;

use App\Command\CreateClient;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Psr\Container\ContainerInterface;

final class CreateClientFactory
{
    /**
     * @param ContainerInterface $container
     * @return CreateClient
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var ClientRepositoryInterface $clientRepository */
        $clientRepository = $container->get(ClientRepositoryInterface::class);

        return new CreateClient($clientRepository);
    }
}
