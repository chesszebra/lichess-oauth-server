<?php

namespace App\Action\Factory;

use App\Action\OAuthToken;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Container\ContainerInterface;

final class OAuthTokenFactory
{
    public function __invoke(ContainerInterface $container): OAuthToken
    {
        /** @var AuthorizationServer $oauthServer */
        $oauthServer = $container->get(AuthorizationServer::class);

        return new OAuthToken($oauthServer);
    }
}
