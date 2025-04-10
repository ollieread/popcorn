<?php

use Popcorn\Container\Container;
use Popcorn\Container\Factories\AutowireFactory;
use Popcorn\Container\Factories\StackedFactory;

if (file_exists(__DIR__ . '/cache/factory.php')) {
    $factory = require __DIR__ . '/cache/factory.php';
} else {
    $factory = new StackedFactory(
    /** @phpstan-ignore-next-line */
        $manual = require __DIR__ . '/cache/dependencies.manual.php',
        new AutowireFactory()
    );
}

/** @var \Popcorn\Container\Contracts\Factory $factory */

return new Container($factory);
