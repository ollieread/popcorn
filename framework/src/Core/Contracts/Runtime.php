<?php

namespace Popcorn\Core\Contracts;

use Popcorn\DI\_Pre\Contracts\ServiceContainer;

interface Runtime
{
    /**
     * Set the service container for use in the runtime.
     *
     * @param \Popcorn\DI\_Pre\Contracts\ServiceContainer $container
     *
     * @return static
     */
    public function setServiceContainer(ServiceContainer $container): static;

    /**
     * Boot the runtime.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Run the runtime.
     *
     * @return void
     */
    public function run(): void;
}
