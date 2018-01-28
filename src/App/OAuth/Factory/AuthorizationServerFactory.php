<?php

namespace App\OAuth\Factory;

use DateInterval;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Psr\Container\ContainerInterface;

final class AuthorizationServerFactory
{
    /**
     * @param ContainerInterface $container
     * @return AuthorizationServer
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var ClientRepositoryInterface $clientRepository */
        $clientRepository = $container->get(ClientRepositoryInterface::class);

        /** @var AccessTokenRepositoryInterface $accessTokenRepository */
        $accessTokenRepository = $container->get(AccessTokenRepositoryInterface::class);

        /** @var ScopeRepositoryInterface $scopeRepository */
        $scopeRepository = $container->get(ScopeRepositoryInterface::class);

        /** @var RefreshTokenRepositoryInterface $refreshTokenRepository */
        $refreshTokenRepository = $container->get(RefreshTokenRepositoryInterface::class);

        /** @var AuthCodeRepositoryInterface $authCodeRepository */
        $authCodeRepository = $container->get(AuthCodeRepositoryInterface::class);

        /** @var array $config */
        $config = $container->get('config');

        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $config['private_key_path'],
            $config['encryption_key'],
            $responseType = null
        );

        $server->enableGrantType(
            new AuthCodeGrant(
                $authCodeRepository,
                $refreshTokenRepository,
                new DateInterval('PT10M')
            ),
            new DateInterval('PT1H')
        );

        return $server;
    }
}
