<?php

namespace App\Command\Factory;

use App\Command\ClearTokens;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Psr\Container\ContainerInterface;

final class ClearTokensFactory
{
    /**
     * @param ContainerInterface $container
     * @return ClearTokens
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var AccessTokenRepositoryInterface $accessTokenRepository */
        $accessTokenRepository = $container->get(AccessTokenRepositoryInterface::class);

        /** @var RefreshTokenRepositoryInterface $refreshTokenRepository */
        $refreshTokenRepository = $container->get(RefreshTokenRepositoryInterface::class);

        /** @var AuthCodeRepositoryInterface $authCodeRepository */
        $authCodeRepository = $container->get(AuthCodeRepositoryInterface::class);

        return new ClearTokens($accessTokenRepository, $refreshTokenRepository, $authCodeRepository);
    }
}
