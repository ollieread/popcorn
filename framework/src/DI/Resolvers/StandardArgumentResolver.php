<?php
declare(strict_types=1);

namespace Popcorn\DI\Resolvers;

use Popcorn\DI\Contracts\ArgumentResolver;
use Popcorn\DI\Contracts\ContextAwareArgumentResolver;
use Popcorn\DI\Contracts\ServiceContainer;
use Popcorn\DI\Exceptions\ServiceContainerException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;
use Throwable;

final class StandardArgumentResolver implements ArgumentResolver, ContextAwareArgumentResolver
{
    private ?string $function = null;

    /**
     * @var class-string|null
     */
    private ?string $service = null;

    /**
     * Resolve the argument for the given parameter.
     *
     * @param \ReflectionParameter                   $parameter
     * @param \Popcorn\DI\Contracts\ServiceContainer $container
     *
     * @return mixed
     */
    public function resolve(ReflectionParameter $parameter, ServiceContainer $container): mixed
    {
        assert(
            $this->function !== null,
            new RuntimeException('No parameter resolution context provider')
        );

        $type = $parameter->getType();

        // This is here as I'd like to support intersection types in the future,
        // though it throws an exception for now.
        if ($type instanceof ReflectionIntersectionType) {
            return $this->handleIntersectionType($type, $parameter, $container);
        }

        // If this isn't a named type, or it's built-in (not a class),
        // we just throw an exception.
        if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
            throw ServiceContainerException::unresolvableParameter(
                $parameter->getName(),
                $this->function,
                $this->service
            );
        }

        /**
         * We can be confident that if we're here, it's a valid class name.
         *
         * @var class-string $className
         */
        $className = $type->getName();

        try {
            return $container->get($className);
        } catch (Throwable $throwable) {
            // There's a chance that we'll get exceptions through while
            // resolving the class, so we need to catch them and wrap them,
            // otherwise we'll lose the context of the parameter.
            // Plus, there's nothing worse than an exception that doesn't
            // exactly make sense.
            throw ServiceContainerException::unresolvableParameter(
                $parameter->getName(),
                $this->function,
                $this->service,
                $throwable
            );
        }
    }

    /**
     * Set the current context.
     *
     * @param string            $function
     * @param class-string|null $service
     *
     * @return void
     */
    public function setContext(string $function, ?string $service = null): void
    {
        $this->function = $function;
        $this->service  = $service;
    }

    /**
     * Flush the current context.
     *
     * @return void
     */
    public function flushContext(): void
    {
        $this->function = null;
        $this->service  = null;
    }

    private function handleIntersectionType(ReflectionIntersectionType $type, ReflectionParameter $parameter, ServiceContainer $container): mixed
    {
        assert(
            $this->function !== null,
            new RuntimeException('No parameter resolution context provider')
        );

        throw ServiceContainerException::unresolvableParameter(
            $parameter->getName(),
            $this->function,
            $this->service
        );
    }
}
