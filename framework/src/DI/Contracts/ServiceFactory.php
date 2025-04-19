<?php

namespace Popcorn\DI\Contracts;

/**
 * @template TService of object
 */
interface ServiceFactory
{
    /**
     * Make a service instance.
     *
     * @param \Popcorn\DI\Contracts\ServiceContainer $container
     *
     * @return object
     *
     * @phpstan-return TService
     */
    public function make(ServiceContainer $container): object;
}
