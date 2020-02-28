<?php

namespace App;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use PDO;

return [
    'authenticate_url' => 'https://lichess.org/login?referrer=%s',
    'authenticate_cookie' => 'lila2',
    'check_authentication_url' => 'https://lichess.org/account/info',
    'private_key_path' => __DIR__ . '/../../data/private.key',
    'encryption_key' => '',

    'grant_enabled_auth_code' => true,
    'grant_enabled_client_credentials' => false,
    'grant_enabled_refresh_token' => true,

    // Based on https://en.wikipedia.org/wiki/ISO_8601#Durations
    'ttl_access_token' => 'PT1H',
    'ttl_auth_code' => 'PT10M',
    'ttl_refresh_token' => 'P1M',

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
