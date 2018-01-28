<?php

namespace App\OAuth\Repository\Pdo;

use App\OAuth\Entity\RefreshToken as RefreshTokenEntity;
use App\OAuth\ExpirableTokensInterface;
use DateTime;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use PDO;

final class RefreshToken implements RefreshTokenRepositoryInterface, ExpirableTokensInterface
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
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $data = [
            'id' => $refreshTokenEntity->getIdentifier(),
            'access_token_id' => $refreshTokenEntity->getAccessToken()->getIdentifier(),
            'expire_date' => $refreshTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s'),
        ];

        $sql = sprintf(
            'INSERT INTO oauth_refresh_token (%s) VALUES (%s)',
            implode(', ', array_keys($data)),
            implode(', ', array_fill(0, count($data), '?'))
        );

        $statement = $this->connection->prepare($sql);
        $statement->execute(array_values($data));
    }

    /**
     * Revoke the refresh token.
     *
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId)
    {
        $statement = $this->connection->prepare('DELETE FROM oauth_refresh_token WHERE id = :id');
        $statement->bindValue('id', $tokenId);
        $statement->execute();
    }

    /**
     * Check if the refresh token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        $statement = $this->connection->prepare('SELECT expire_date FROM oauth_refresh_token WHERE id = :id');
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
        $statement = $this->connection->prepare('DELETE FROM oauth_refresh_token WHERE expire_date <= NOW()');
        $statement->execute();
    }
}
