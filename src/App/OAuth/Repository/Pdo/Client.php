<?php

namespace App\OAuth\Repository\Pdo;

use App\OAuth\Entity\Client as ClientEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use PDO;

final class Client implements ClientRepositoryInterface
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
     * Get a client.
     *
     * @param string $clientIdentifier The client's identifier
     * @param string $grantType The grant type used
     * @param null|string $clientSecret The client's secret (if sent)
     * @param bool $mustValidateSecret If true the client must attempt to validate the secret if the client
     *                                        is confidential
     *
     * @return ClientEntityInterface
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        $statement = $this->connection->prepare("SELECT * FROM oauth_client WHERE client_id = :id");
        $statement->bindValue('id', $clientIdentifier);
        $statement->execute();

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $uris = (array)json_decode($data['redirect_uri'], true);

        $client = new ClientEntity(
            $data['client_id'],
            $data['client_secret'],
            $data['name'],
            $uris
        );

        $scopes = array_filter(explode(' ', $data['scopes']));

        foreach ($scopes as $scope) {
            $client->allowScope($scope);
        }

        return $client;
    }
}
