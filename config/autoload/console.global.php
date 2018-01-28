<?php

namespace App;

return [
    'dependencies' => [
        'factories' => [
            Command\ClearTokens::class => Command\Factory\ClearTokensFactory::class,
        ],
    ],

    'console' => [
        'commands' => [
            Command\ClearTokens::class,
        ],
    ],
];
