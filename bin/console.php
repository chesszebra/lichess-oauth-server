<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

/** @var \Interop\Container\ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';

$commands = $container->get('config')['console']['commands'];

$application = new Application('Application Console');

foreach ($commands as $command) {
    $application->add($container->get($command));
}

$application->run();
