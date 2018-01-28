<?php

namespace App\OAuth\Repository\Pdo;

use App\OAuth\Entity\AccessToken as AccessTokenEntity;
use App\OAuth\ExpirableTokensInterface;
use DateTime;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use PDO;

final class AccessToken implements AccessTokenRepositoryInterface, ExpirableTokensInterface
{
    /**
     * @var PDO
     */
    private $connection;

    /**
     * Initializes a new instance of this class.
     *
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
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
            'id' => $accessTokenEntity->getIdentifier(),
            'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
            'user_id' => $accessTokenEntity->getUserIdentifier(),
            'expire_date' => $accessTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s'),
            'scopes' => implode(' ', $scopes),
        ];

        $sql = sprintf(
            'INSERT INTO oauth_access_token (%s) VALUES (%s)',
            implode(', ', array_keys($data)),
            implode(', ', array_fill(0, count($data), '?'))
        );

        $statement = $this->connection->prepare($sql);
        $statement->execute(array_values($data));
    }

    /**
     * Revoke an access token.
     *
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId)
    {
        $statement = $this->connection->prepare('DELETE FROM oauth_access_token WHERE id = :id');
        $statement->bindValue('id', $tokenId);
        $statement->execute();
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isAccessTokenRevoked($tokenId)
    {
        $statement = $this->connection->prepare('SELECT expire_date FROM oauth_access_token WHERE id = :id');
        $statement->bindValue('id', $tokenId);
        $statement->execute();

        $result = $statement->fetchColumn();

        if (!$result) {
            return true;
        }

        $date = DateTime::createFromFormat('Y-m-d H:i:s', $result);
        $now = new DateTime();

        return $date <= $now;
    }

    public function clearExpiredTokens()
    {
        $statement = $this->connection->prepare('DELETE FROM oauth_access_token WHERE expire_date <= NOW()');
        $statement->execute();
    }
}
