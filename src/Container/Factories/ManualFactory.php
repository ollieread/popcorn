<?php
declare(strict_types=1);

namespace Popcorn\Container\Factories;

use Popcorn\Container\Contracts\Factory;
use RuntimeException;

final class ManualFactory implements Factory
{
    /**
     * @var array<class-string, \Closure(static): object>
     */
    private array $services;

    /**
     * @var array<class-string, object>
     */
    private array $instances;

    /**
     * @param array<class-string, \Closure(static): object> $services
     */
    public function __construct(array $services)
    {
        $this->services = $services;
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
        return isset($this->services[$class]);
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
        if (! isset($this->instances[$class])) {
            $resolver = $this->services[$class] ?? null;

            if ($resolver === null) {
                throw new RuntimeException(sprintf('Service "%s" not found.', $class));
            }

            $this->instances[$class] = $resolver($this);
        }

        /** @var TClass $instance */
        $instance = $this->instances[$class];

        return $instance;
    }
}
