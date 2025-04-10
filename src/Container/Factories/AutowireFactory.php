<?php
declare(strict_types=1);

namespace Popcorn\Container\Factories;

use Popcorn\Container\Contracts\Factory;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use RuntimeException;

final class AutowireFactory implements Factory
{
    /**
     * @var array<class-string, object>
     */
    private array $instances = [];

    /**
     * Check if a service is registered in the container.
     *
     * @param class-string $class
     *
     * @return bool
     */
    public function has(string $class): bool
    {
        return class_exists($class);
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
            $this->instances[$class] = $this->resolve($class);
        }

        /** @var TClass $instance */
        $instance = $this->instances[$class];

        return $instance;
    }

    /**
     * Resolve a service for the container.
     *
     * @template TClass of object
     *
     * @param class-string<TClass> $class The service to resolve
     *
     * @return object The resolved service
     *
     * @phpstan-return TClass The resolved service
     */
    private function resolve(string $class): object
    {
        try {
            $reflection = new ReflectionClass($class);

            $constructor = $reflection->getConstructor();

            if ($constructor === null) {
                return new $class();
            }

            $arguments = [];

            foreach ($constructor->getParameters() as $parameter) {
                $type = $parameter->getType();

                if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
                    throw new RuntimeException(sprintf(
                        'Unable to resolve parameter "%s" in class "%s": %s',
                        $parameter->getName(),
                        $class,
                        'Parameter type is not a class or is a built-in type.'
                    ));
                }

                $arguments[$parameter->getName()] = $type->getName();
            }

            return $reflection->newInstanceArgs(array_map(
                function (string $type): object {
                    /** @var class-string $type */
                    return $this->get($type);
                },
                $arguments
            ));
        } catch (ReflectionException $e) {
            throw new RuntimeException(sprintf(
                'Unable to resolve class "%s": %s',
                $class,
                $e->getMessage()
            ), previous: $e);
        }
    }
}
