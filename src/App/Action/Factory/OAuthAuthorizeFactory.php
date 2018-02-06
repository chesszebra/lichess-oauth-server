<?php

namespace App\Action\Factory;

use App\Action\OAuthAuthorize;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

final class OAuthAuthorizeFactory
{
    /**
     * @param ContainerInterface $container
     * @return OAuthAuthorize
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
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
