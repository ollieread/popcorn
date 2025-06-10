<?php
declare(strict_types=1);

use Popcorn\Core\Bootstrappers;
use Popcorn\Core\Config\AppConfig;
use Popcorn\Core\CoreServiceProvider;
use Popcorn\Core\Popcorn;
use Popcorn\Http\HttpServiceProvider;

return Popcorn::builder()
              ->useCacheIn(__DIR__ . '/cache')
              ->loadEnvFrom(dirname(__DIR__) . '/.env.php')
              ->usingBootstrappers([
                  // Load the service container and configure it by collecting
                  // services using the service collector and service providers.
                  Bootstrappers\LoadAndConfigureServiceContainer::class,
                  // Load the environment variables from a '.env.php' file.
                  Bootstrappers\PopulateEnvVarsFromEnvPhp::class,
                  // Load the config objects from the map provided below
                  // in the 'loadConfigFrom()' method.
                  Bootstrappers\LoadConfigObjectsFromFiles::class,
              ])
              ->loadConfigFrom([
                  AppConfig::class => dirname(__DIR__) . '/config/app.php',
              ])
              ->usingProviders([
                  // Register all the core services.
                  CoreServiceProvider::class,
                  // Register the HTTP services.
                  HttpServiceProvider::class,
                  // Register the application services.
                  App\AppServiceProvider::class,
                  // Add extra providers here
              ])
              ->build();
