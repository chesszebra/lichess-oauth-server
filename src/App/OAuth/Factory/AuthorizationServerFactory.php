<?php

namespace App\OAuth\Factory;

use App\OAuth\ResponseTypes\BearerTokenResponse;
use DateInterval;
use Exception;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
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
     * @throws Exception
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
            new BearerTokenResponse()
        );

        if ($config['grant_enabled_auth_code']) {
            $server->enableGrantType(
                $this->createAuthCodeGrant($config, $authCodeRepository, $refreshTokenRepository),
                new DateInterval($config['ttl_access_token'])
            );
        }

        if ($config['grant_enabled_client_credentials']) {
            $server->enableGrantType(
                new ClientCredentialsGrant(),
                new DateInterval($config['ttl_access_token'])
            );
        }

        if ($config['grant_enabled_refresh_token']) {
            $server->enableGrantType(
                $this->createRefreshTokenGrant($config, $refreshTokenRepository),
                new DateInterval($config['ttl_access_token'])
            );
        }

        return $server;
    }

    /**
     * @throws Exception
     */
    private function createAuthCodeGrant(
        array $config,
        AuthCodeRepositoryInterface $authCodeRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository
    ): GrantTypeInterface {
        return new AuthCodeGrant(
            $authCodeRepository,
            $refreshTokenRepository,
            new DateInterval($config['ttl_auth_code'])
        );
    }

    /**
     * @throws Exception
     */
    private function createRefreshTokenGrant(
        array $config,
        RefreshTokenRepositoryInterface $refreshTokenRepository
    ): GrantTypeInterface {
        $grant = new RefreshTokenGrant($refreshTokenRepository);
        $grant->setRefreshTokenTTL(new DateInterval($config['ttl_refresh_token']));

        return $grant;
    }
}
