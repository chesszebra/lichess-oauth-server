<?php

namespace App\Action;

use App\OAuth\Entity\Client;
use App\OAuth\Entity\User;
use Exception;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ServerRequestInterface;
use Twig_Error_Loader;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Stream;
use Zend\Expressive\Template\TemplateRendererInterface;

final class OAuthAuthorize implements ServerMiddlewareInterface
{
    /**
     * The url to redirect to when the user is not authenticated.
     *
     * @var string
     */
    private $authenticateUrl;

    /**
     * The OAuth authorization server.
     *
     * @var AuthorizationServer
     */
    private $oauthServer;

    /**
     * The template renderer.
     *
     * @var TemplateRendererInterface
     */
    private $template;

    public function __construct(
        string $authenticateUrl,
        AuthorizationServer $oauthServer,
        TemplateRendererInterface $template = null
    ) {
        $this->authenticateUrl = $authenticateUrl;
        $this->oauthServer = $oauthServer;
        $this->template = $template;
    }

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return \Psr\Http\Message\ResponseInterface|Response|HtmlResponse|RedirectResponse|static
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if (!$this->isAuthenticated()) {
            return new RedirectResponse($this->authenticateUrl);
        }

        $response = new Response();

        try {
            /** @var AuthorizationRequest $AuthorizationRequest */
            $authorizationRequest = $this->oauthServer->validateAuthorizationRequest($request);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (Exception $exception) {
            $stream = new Stream('php://temp', 'w');
            $stream->write($exception->getMessage());

            return $response->withBody($stream)->withStatus(500);
        }

        if ($request->getMethod() === 'POST') {
            $postData = $request->getParsedBody();
            $approved = array_key_exists('authorize', $postData);

            $authorizationRequest->setAuthorizationApproved($approved);
            $authorizationRequest->setUser(new User($this->getUserIdentifier()));

            try {
                return $this->oauthServer->completeAuthorizationRequest($authorizationRequest, $response);
            } catch (OAuthServerException $exception) {
                return $exception->generateHttpResponse($response);
            } catch (Exception $exception) {
                $stream = new Stream('php://temp', 'w');
                $stream->write($exception->getMessage());

                return $response->withBody($stream)->withStatus(500);
            }
        }

        $client = $authorizationRequest->getClient();

        $parameters = [
            'applicationName' => $client->getName(),
            'client' => $client,
            'scopes' => $this->getValidRequestedScopes($client, $authorizationRequest->getScopes()),
        ];

        try {
            $html = $this->template->render('app::oauth-authorize-custom', $parameters);
        } catch (Twig_Error_Loader $e) {
            $html = $this->template->render('app::oauth-authorize', $parameters);
        }

        return new HtmlResponse($html);
    }

    public function getValidRequestedScopes(Client $client, array $scopes)
    {
        $result = [];

        /** @var ScopeEntityInterface $scope */
        foreach ($scopes as $scope) {
            if (in_array($scope->getIdentifier(), $client->getScopes())) {
                $result[] = $scope->getIdentifier();
            }
        }

        return $result;
    }

    private function isAuthenticated()
    {
        return true;
    }

    private function getUserIdentifier()
    {
        return 42;
    }
}
