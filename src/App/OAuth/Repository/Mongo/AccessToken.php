<?php

namespace App\OAuth\Repository\Mongo;

use App\OAuth\Entity\AccessToken as AccessTokenEntity;
use App\OAuth\ExpirableTokensInterface;
use DateTime;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

final class AccessToken implements AccessTokenRepositoryInterface, ExpirableTokensInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * Initializes a new instance of this class.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Create a new access token
     *
     * @param ClientEntityInterface $clientEntity
     * @param ScopeEntityInterface[] $scopes
     * @param mixed $userIdentifier
     *
     * @return AccessTokenEntityInterface
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        return new AccessTokenEntity();
    }

    /**
     * Persists a new access token to permanent storage.
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     *
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $scopes = [];

        /** @var ScopeEntityInterface $scope */
        foreach ($accessTokenEntity->getScopes() as $scope) {
            $scopes[] = $scope->getIdentifier();
        }

        $data = [
            'access_token_id' => $accessTokenEntity->getIdentifier(),
            'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
            'user_id' => $accessTokenEntity->getUserIdentifier(),
            'expire_date' => new UTCDateTime($accessTokenEntity->getExpiryDateTime()),
            'scopes' => $scopes,
        ];

        $this->collection->insertOne($data);
    }

    /**
     * Revoke an access token.
     *
     * @param string $tokenId
     * @throws \MongoDB\Exception\UnsupportedException
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     */
    public function revokeAccessToken($tokenId)
    {
        $this->collection->deleteOne([
            'access_token_id' => $tokenId,
        ]);
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     * @throws \MongoDB\Exception\UnsupportedException
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     */
    public function isAccessTokenRevoked($tokenId)
    {
        $result = $this->collection->findOne([
            'access_token_id' => $tokenId,
        ]);

        if (!$result) {
            return true;
        }

        if (!$result['expire_date'] instanceof UTCDateTime) {
            return true;
        }

        $date = $result['expire_date']->toDateTime();
        $now = new DateTime();

        return $date <= $now;
    }

    /**
     *
     * @throws \MongoDB\Exception\UnsupportedException
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     */
    public function clearExpiredTokens()
    {
        $this->collection->deleteMany([
            'expire_date' => [
                '$lte' => new UTCDateTime(),
            ],
        ]);
    }
}
