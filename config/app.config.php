<?php

/**
 * App Configuration
 * =========================
 *
 * This file contains the core application config.
 *
 * @var \Popcorn\Config\EnvVars $env The environment variables
 */

return new \Popcorn\Config\AppConfig(
    environment: $env->get('APP_ENV', 'local'),
    debug      : $env->get('DEBUG', false)
);
