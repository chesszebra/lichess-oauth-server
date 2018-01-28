<?php

namespace App;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use PDO;

return [
    'authenticate_url' => 'https://lichess.org',
    'private_key_path' => __DIR__ . '/../../data/private.key',
    'encryption_key' => '',

    'pdo' => [
        'dsn' => '',
        'username' => '',
        'password' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ],
    ],

    'dependencies' => [
        'factories' => [
            AuthCodeRepositoryInterface::class => OAuth\Repository\Pdo\Factory\AuthCodeFactory::class,
            AccessTokenRepositoryInterface::class => OAuth\Repository\Pdo\Factory\AccessTokenFactory::class,
            ClientRepositoryInterface::class => OAuth\Repository\Pdo\Factory\ClientFactory::class,
            PDO::class => Factory\PdoFactory::class,
            RefreshTokenRepositoryInterface::class => OAuth\Repository\Pdo\Factory\RefreshTokenFactory::class,
            ScopeRepositoryInterface::class => OAuth\Repository\Pdo\Factory\ScopeFactory::class,
        ],
    ],
];
