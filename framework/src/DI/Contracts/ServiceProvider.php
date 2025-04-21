<?php

namespace Popcorn\DI\Contracts;

interface ServiceProvider
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
    public function register(ServiceCollector $collector): void;
}
