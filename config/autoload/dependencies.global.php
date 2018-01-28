<?php

namespace App;

use MongoDB\Client;
use PDO;
use Zend\Expressive\Application;
use Zend\Expressive\Container;
use Zend\Expressive\Delegate;
use Zend\Expressive\Helper;
use Zend\Expressive\Middleware;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Stratigility\Middleware\ErrorHandler;

return [
    'dependencies' => [
        'aliases' => [
            'Zend\Expressive\Delegate\DefaultDelegate' => Delegate\NotFoundDelegate::class,
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
