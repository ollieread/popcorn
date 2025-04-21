<?php

namespace Popcorn\DI\Contracts;

/**
 * @template TService of object
 */
interface ServiceFactory
{
    /**
     * Create a service instance.
     *
     * @param \Popcorn\DI\Contracts\ServiceContainer $container
     *
     * @return object|null
     *
     * @phpstan-return TService|null
     */
    public function make(ServiceContainer $container): ?object;
}
