<?php

namespace App\OAuth;

use App\OAuth\Entity\Client;

interface ClientCreatorInterface
{
    /**
     * Creates a new client.
     *
     * @param Client $client
     * @return void
     */
    public function createClient(Client $client);
}
