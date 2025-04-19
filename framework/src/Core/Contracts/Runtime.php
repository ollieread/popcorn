<?php

namespace Popcorn\Core\Contracts;

use Popcorn\DI\Contracts\ServiceContainer;

interface Runtime
{
    /**
     * Set the service container for use in the runtime.
     *
     * @param \Popcorn\DI\Contracts\ServiceContainer $container
     *
     * @return static
     */
    public function setServiceContainer(ServiceContainer $container): static;

    /**
     * Run the runtime.
     *
     * @return void
     */
    public function run(): void;
}
