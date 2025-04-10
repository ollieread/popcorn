<?php

namespace Popcorn\Container\Contracts;

/**
 * Container Factory
 * =================
 *
 * Container factories are responsible for creating and storing instances of
 * services.
 *
 * @package Container
 */
interface Factory
{
    /**
     * Check if a service is registered in the container.
     *
     * @param class-string $class
     *
     * @return bool
     */
    public function has(string $class): bool;

    /**
     * Get a service from the container.
     *
     * @template TClass of object
     *
     * @param class-string<TClass> $class The service to resolve
     *
     * @return object The resolved service
     *
     * @phpstan-return TClass The resolved service
     */
    public function get(string $class): object;
}
