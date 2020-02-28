<?php

namespace App\Action\Factory;

use App\Action\OAuthAuthorize;
use League\OAuth2\Server\AuthorizationServer;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

final class OAuthAuthorizeFactory
{
    public function __invoke(ContainerInterface $container): OAuthAuthorize
    {
        /** @var array $config */
        $config = $container->get('config');

        /** @var AuthorizationServer $oauthServer */
        $oauthServer = $container->get(AuthorizationServer::class);

        /** @var TemplateRendererInterface $template */
        $template = $container->get(TemplateRendererInterface::class);

        return new OAuthAuthorize(
            $config['authenticate_url'],
            $config['authenticate_cookie'],
            $config['check_authentication_url'],
            $oauthServer,
            $template
        );
    }
}
