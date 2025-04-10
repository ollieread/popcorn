<?php
declare(strict_types=1);

namespace Popcorn\Container;

use Popcorn\Container\Contracts\Factory;

final class Container implements Contracts\Container
{
    /**
     * @var \Popcorn\Container\Contracts\Factory
     */
    private Factory $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get the container's factory.
     *
     * @return \Popcorn\Container\Contracts\Factory
     */
    public function factory(): Factory
    {
        return $this->factory;
    }

    /**
     * Check if a service is registered in the container.
     *
     * @param class-string $class
     *
     * @return bool
     */
    public function has(string $class): bool
    {
        return $this->factory()->has($class);
    }

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
    public function get(string $class): object
    {
        return $this->factory()->get($class);
    }
}
