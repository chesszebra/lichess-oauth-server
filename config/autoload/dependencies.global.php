<?php

namespace App;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\Application;
use Mezzio\Container;
use Mezzio\Delegate;
use Mezzio\Helper;
use Mezzio\Middleware;
use MongoDB\Client;
use PDO;

return [
    'dependencies' => [
        'aliases' => [
            'Mezzio\Delegate\DefaultDelegate' => Delegate\NotFoundDelegate::class,
        ],
        'factories' => [
            Application::class => Container\ApplicationFactory::class,
            Client::class => Factory\MongoClientFactory::class,
            Delegate\NotFoundDelegate::class => Container\NotFoundDelegateFactory::class,
            ErrorHandler::class => Container\ErrorHandlerFactory::class,
            Helper\ServerUrlHelper::class => InvokableFactory::class,
            Helper\ServerUrlMiddleware::class => Helper\ServerUrlMiddlewareFactory::class,
            Helper\UrlHelper::class => Helper\UrlHelperFactory::class,
            Helper\UrlHelperMiddleware::class => Helper\UrlHelperMiddlewareFactory::class,
            PDO::class => Factory\PdoFactory::class,
            Middleware\ErrorResponseGenerator::class => Container\ErrorResponseGeneratorFactory::class,
            Middleware\NotFoundHandler::class => Container\NotFoundHandlerFactory::class,
        ],
    ],
];
