<?php

namespace App\OAuth\Repository\Mongo;

use App\OAuth\Entity\RefreshToken as RefreshTokenEntity;
use App\OAuth\ExpirableTokensInterface;
use DateTime;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

final class RefreshToken implements RefreshTokenRepositoryInterface, ExpirableTokensInterface
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
     * Creates a new refresh token
     *
     * @return RefreshTokenEntityInterface
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }

    /**
     * Create a new refresh token_name.
     *
     * @param RefreshTokenEntityInterface $refreshTokenEntity
     *
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $data = [
            'refresh_token_id' => $refreshTokenEntity->getIdentifier(),
            'access_token_id' => $refreshTokenEntity->getAccessToken()->getIdentifier(),
            'expire_date' => new UTCDateTime($refreshTokenEntity->getExpiryDateTime()->getTimestamp() * 1000),
        ];

        $this->collection->insertOne($data);
    }

    /**
     * Revoke the refresh token.
     *
     * @param string $tokenId
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     */
    public function revokeRefreshToken($tokenId)
    {
        $this->collection->deleteOne([
            'refresh_token_id' => $tokenId,
        ]);
    }

    /**
     * Check if the refresh token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        $result = $this->collection->findOne([
            'refresh_token_id' => $tokenId,
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
