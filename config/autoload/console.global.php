<?php

namespace App;

return [
    'dependencies' => [
        'factories' => [
            Command\ClearTokens::class => Command\Factory\ClearTokensFactory::class,
            Command\CreateClient::class => Command\Factory\CreateClientFactory::class,
        ],
    ],

    'console' => [
        'commands' => [
            Command\ClearTokens::class,
            Command\CreateClient::class,
        ],
    ],
];
