<?php

namespace App;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use PDO;

return [
    'authenticate_url' => 'https://lichess.org/login?referer=%s',
    'authenticate_cookie' => 'lila2',
    'check_authentication_url' => 'https://lichess.org/account/info',
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

    'mongodb' => [
        'uri' => 'mongodb://127.0.0.1/',
        'uriOptions' => [],
        'driverOptions' => [],
        'database' => 'lichess',
        'collections' => [
            'access_token' => 'oauth_access_token',
            'authorization_code' => 'oauth_authorization_code',
            'client' => 'oauth_client',
            'refresh_token' => 'oauth_refresh_token',
        ],
    ],

    'dependencies' => [
        'factories' => [
            AuthCodeRepositoryInterface::class => OAuth\Repository\Pdo\Factory\AuthCodeFactory::class,
            AccessTokenRepositoryInterface::class => OAuth\Repository\Pdo\Factory\AccessTokenFactory::class,
            ClientRepositoryInterface::class => OAuth\Repository\Pdo\Factory\ClientFactory::class,
            RefreshTokenRepositoryInterface::class => OAuth\Repository\Pdo\Factory\RefreshTokenFactory::class,
            ScopeRepositoryInterface::class => OAuth\Repository\Pdo\Factory\ScopeFactory::class,
        ],
    ],
];
