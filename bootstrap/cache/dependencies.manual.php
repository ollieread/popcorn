<?php
declare(strict_types=1);

use Popcorn\Config\AppConfig;
use Popcorn\Config\EnvVars;
use Popcorn\Container\Contracts\Factory;
use Popcorn\Container\Factories\ManualFactory;

return new ManualFactory([
    EnvVars::class   => static function (Factory $f): EnvVars {
        /** @var array<string, scalar> $env */
        $env = require dirname(__DIR__) . '/.env.php';
        return new EnvVars($env);
    },
    AppConfig::class => static function (Factory $f): AppConfig {
        $env = $f->get(EnvVars::class);
        /** @var \Popcorn\Config\AppConfig */
        return require dirname(__DIR__) . '/config/app.config.php';
    },
]);
