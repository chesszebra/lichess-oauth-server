<?php

namespace App\OAuth\Repository\Pdo;

use App\OAuth\ClientCreatorInterface;
use App\OAuth\Entity\Client as ClientEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use PDO;

final class Client implements ClientRepositoryInterface, ClientCreatorInterface
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
    public function getClientEntity($clientIdentifier, $grantType = null, $clientSecret = null, $mustValidateSecret = true)
    {
        $statement = $this->connection->prepare("SELECT * FROM oauth_client WHERE client_id = :id");
        $statement->bindValue('id', $clientIdentifier);
        $statement->execute();

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        if ($mustValidateSecret && $data['client_secret'] !== $clientSecret) {
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

    /**
     * Creates a new client.
     *
     * @param ClientEntity $client
     * @return void
     */
    public function createClient(ClientEntity $client)
    {
        $data = [
            'client_id' => $client->getIdentifier(),
            'client_secret' => $client->getClientSecret(),
            'name' => $client->getName(),
            'redirect_uri' => json_encode($client->getRedirectUri()),
            'scopes' => implode(' ', $client->getScopes()),
        ];

        $sql = sprintf(
            'INSERT INTO oauth_client (%s) VALUES (%s)',
            implode(', ', array_keys($data)),
            implode(', ', array_fill(0, count($data), '?'))
        );

        $statement = $this->connection->prepare($sql);
        $statement->execute(array_values($data));
    }
}
