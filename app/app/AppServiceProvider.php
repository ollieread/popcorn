<?php
declare(strict_types=1);

namespace App;

use Popcorn\DI\Attributes\NoAutowiring;
use Popcorn\DI\Contracts\ServiceCollector;
use Popcorn\DI\Contracts\ServiceProvider;

#[NoAutowiring]
final class AppServiceProvider implements ServiceProvider
{
    /**
     * Register services.
     *
     * This method is called when the {@see \Popcorn\DI\Contracts\ServiceContainer}
     * is being created.
     * It is used to register services, bindings, factories, etc.
     *
     * @param \Popcorn\DI\Contracts\ServiceCollector $collector
     *
     * @return void
     */
    public function register(ServiceCollector $collector): void
    {
        // Register this directory as containing services to be discovered.
        $collector->discover('App', __DIR__);

        // Exclude this file from service discovery.
        $collector->exclude(__FILE__);
    }
}
