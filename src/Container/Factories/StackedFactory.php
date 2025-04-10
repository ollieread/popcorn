<?php
declare(strict_types=1);

namespace Popcorn\Container\Factories;

use Popcorn\Container\Contracts\Factory;
use RuntimeException;

final class StackedFactory implements Factory
{
    /**
     * @var array<\Popcorn\Container\Contracts\Factory>
     */
    private array $factories;

    public function __construct(Factory ...$factories)
    {
        assert(count($factories) > 1, 'At least two factories are required.');

        $this->factories = $factories;
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
        return array_any($this->factories, static fn(Factory $factory) => $factory->has($class));
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
        $factory = array_find($this->factories, static fn(Factory $factory) => $factory->has($class));

        if ($factory === null) {
            throw new RuntimeException(sprintf('Service "%s" not found in any factory.', $class));
        }

        return $factory->get($class);
    }
}
