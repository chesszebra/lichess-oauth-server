<?php

namespace App\OAuth\Repository\Mongo;

use App\OAuth\Entity\AuthCode as AuthCodeEntity;
use App\OAuth\ExpirableTokensInterface;
use DateTime;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

final class AuthCode implements AuthCodeRepositoryInterface, ExpirableTokensInterface
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
     * Creates a new AuthCode
     *
     * @return AuthCodeEntityInterface
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }

    /**
     * Persists a new auth code to permanent storage.
     *
     * @param AuthCodeEntityInterface $authCodeEntity
     *
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $scopes = [];

        /** @var ScopeEntityInterface $scope */
        foreach ($authCodeEntity->getScopes() as $scope) {
            $scopes[] = $scope->getIdentifier();
        }

        $data = [
            'auth_code_id' => $authCodeEntity->getIdentifier(),
            'client_id' => $authCodeEntity->getClient()->getIdentifier(),
            'user_id' => $authCodeEntity->getUserIdentifier(),
            'expire_date' => new UTCDateTime($authCodeEntity->getExpiryDateTime()),
            'redirect_uri' => $authCodeEntity->getRedirectUri(),
            'scopes' => $scopes,
        ];

        $this->collection->insertOne($data);
    }

    /**
     * Revoke an auth code.
     *
     * @param string $codeId
     * @throws \MongoDB\Exception\UnsupportedException
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     */
    public function revokeAuthCode($codeId)
    {
        $this->collection->deleteOne([
            'auth_code_id' => $codeId,
        ]);
    }

    /**
     * Check if the auth code has been revoked.
     *
     * @param string $codeId
     *
     * @return bool Return true if this code has been revoked
     * @throws \MongoDB\Exception\UnsupportedException
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     */
    public function isAuthCodeRevoked($codeId)
    {
        $result = $this->collection->findOne([
            'auth_code_id' => $codeId,
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
