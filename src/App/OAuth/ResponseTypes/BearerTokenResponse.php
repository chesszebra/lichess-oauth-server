<?php

namespace App\OAuth\ResponseTypes;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse as BaseBearerTokenResponse;

final class BearerTokenResponse extends BaseBearerTokenResponse
{
    /**
     * Add custom fields to your Bearer Token response here, then override
     * AuthorizationServer::getResponseType() to pull in your version of
     * this class rather than the default.
     *
     * @param AccessTokenEntityInterface $accessToken
     *
     * @return array
     */
    protected function getExtraParams(AccessTokenEntityInterface $accessToken)
    {
        $scopes = [];

        /** @var ScopeEntityInterface $scope */
        foreach ($accessToken->getScopes() as $scope) {
            $scopes[] = $scope->getIdentifier();
        }

        return [
            'scopes' => implode(' ', $scopes),
        ];
    }
}
