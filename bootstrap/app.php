<?php

use Popcorn\Config\AppConfig;
use Popcorn\Container\Container;
use Popcorn\Container\Contracts\Factory;
use Popcorn\Container\Factories\AutowireFactory;
use Popcorn\Container\Factories\ManualFactory;
use Popcorn\Container\Factories\StackedFactory;

/** @var \Popcorn\Config\AppConfig $config */
$config = require dirname(__DIR__) . '/config/app.config.php';

if (file_exists(__DIR__ . '/cache/factory.php')) {
    $factory = require __DIR__ . '/cache/factory.php';
} else {
    $factory = new StackedFactory(
        new ManualFactory([
            AppConfig::class => static fn (Factory $f): AppConfig => $config,
        ]),
        new AutowireFactory()
    );
}

/** @var \Popcorn\Container\Contracts\Factory $factory */

return new Container($factory);
