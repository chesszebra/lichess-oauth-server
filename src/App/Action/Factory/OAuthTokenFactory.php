<?php

namespace App\Action\Factory;

use App\Action\OAuthToken;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Container\ContainerInterface;

final class OAuthTokenFactory
{
    /**
     * @param ContainerInterface $container
     * @return OAuthToken
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var AuthorizationServer $oauthServer */
        $oauthServer = $container->get(AuthorizationServer::class);

        return new OAuthToken($oauthServer);
    }
}
