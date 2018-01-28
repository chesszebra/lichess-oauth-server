<?php

namespace App\OAuth\Repository\Pdo;

use App\OAuth\Entity\AuthCode as AuthCodeEntity;
use App\OAuth\ExpirableTokensInterface;
use DateTime;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use PDO;

final class AuthCode implements AuthCodeRepositoryInterface, ExpirableTokensInterface
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
            'id' => $authCodeEntity->getIdentifier(),
            'client_id' => $authCodeEntity->getClient()->getIdentifier(),
            'user_id' => $authCodeEntity->getUserIdentifier(),
            'expire_date' => $authCodeEntity->getExpiryDateTime()->format('Y-m-d H:i:s'),
            'redirect_uri' => $authCodeEntity->getRedirectUri(),
            'scopes' => implode(' ', $scopes),
        ];

        $sql = sprintf(
            'INSERT INTO oauth_authorization_code (%s) VALUES (%s)',
            implode(', ', array_keys($data)),
            implode(', ', array_fill(0, count($data), '?'))
        );

        $statement = $this->connection->prepare($sql);
        $statement->execute(array_values($data));
    }

    /**
     * Revoke an auth code.
     *
     * @param string $codeId
     */
    public function revokeAuthCode($codeId)
    {
        $statement = $this->connection->prepare('DELETE FROM oauth_authorization_code WHERE id = :id');
        $statement->bindValue('id', $codeId);
        $statement->execute();
    }

    /**
     * Check if the auth code has been revoked.
     *
     * @param string $codeId
     *
     * @return bool Return true if this code has been revoked
     */
    public function isAuthCodeRevoked($codeId)
    {
        $statement = $this->connection->prepare('SELECT expire_date FROM oauth_authorization_code WHERE id = :id');
        $statement->bindValue('id', $codeId);
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
        $statement = $this->connection->prepare('DELETE FROM oauth_authorization_code WHERE expire_date <= NOW()');
        $statement->execute();
    }
}
