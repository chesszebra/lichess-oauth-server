<?php

namespace App\Action;

use Exception;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use InvalidArgumentException;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

final class OAuthToken implements ServerMiddlewareInterface
{
    /**
     * The OAuth authorization server.
     *
     * @var AuthorizationServer
     */
    private $oauthServer;

    /**
     * Initializes a new instance of this class.
     *
     * @param AuthorizationServer $oauthServer
     */
    public function __construct(AuthorizationServer $oauthServer)
    {
        $this->oauthServer = $oauthServer;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return ResponseInterface
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $response = new Response();

        try {
            return $this->oauthServer->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (Exception $exception) {
            $body = new Stream('php://temp', 'r+');
            $body->write($exception->getMessage());

            return $response->withStatus(500)->withBody($body);
        }
    }
}
