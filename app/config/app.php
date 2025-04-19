<?php
declare(strict_types=1);

use Popcorn\Core\Config\AppConfig;
use Popcorn\Core\EnvVars;

// Make sure that the environment variables are available.
assert(
    isset($env) && $env instanceof EnvVars,
    new RuntimeException('Environment variables are not available for config.')
);

/**
 * Application configuration
 * =========================
 *
 * Contains config specific to the application but required for the framework
 * to run.
 *
 *  - <code>environment</code> The environment, e.g. local, production, etc.
 *  - <code>debug</code> Whether to enable debug mode or not.
 *  - <code>basePath</code> The base path of the application.
 *  - <code>vendorPath</code> The path for the composer vendor directory, defaults to <code>basePath/vendor</code>.
 */
return new AppConfig(
    environment: $env->string('APP_ENV', 'local'),
    debug      : $env->bool('DEBUG', false),
    basePath   : dirname(__DIR__)
);
