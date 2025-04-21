<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Popcorn\DI;

use Closure;
use Popcorn\DI\Contracts\ArgumentResolver;
use Popcorn\DI\Exceptions\ServiceContainerException;
use Popcorn\DI\Exceptions\ServiceNotFound;
use Popcorn\DI\Resolvers\StandardArgumentResolver;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;
use Throwable;

final class ServiceContainer implements Contracts\ServiceContainer
{
    /**
     * The bindings of abstracts to concretes.
     *
     * @var array<class-string, class-string>
     */
    private array $bindings;

    /**
     * The service factory bindings.
     *
     * @var array<class-string, \Popcorn\DI\Contracts\ServiceFactory<object>>
     */
    private array $factories;

    /**
     * The attribute to resolver mapping.
     *
     * @var array<class-string, \Popcorn\DI\Contracts\ArgumentResolver>
     */
    private array $resolvers;

    /**
     * The classes that are scoped.
     *
     * @var list<class-string>
     */
    private array $scoped;

    /**
     * The default argument resolver.
     *
     * @var \Popcorn\DI\Contracts\ArgumentResolver
     */
    private ArgumentResolver $defaultResolver;

    /**
     * Classes that aren't shared and should be resolved each time.
     *
     * @var list<class-string>
     */
    private array $notShared;

    /**
     * Classes that aren't autowired, and either shouldn't be resolved at all
     * or should be resolved manually.
     *
     * @var list<class-string>
     */
    private array $notAutowired;

    /**
     * The instances that have been resolved.
     *
     * @var array<class-string, object>
     */
    private array $instances = [];

    /**
     * @param array<class-string, class-string>                                 $bindings
     * @param array<class-string, \Popcorn\DI\Contracts\ServiceFactory<object>> $factories
     * @param array<class-string, \Popcorn\DI\Contracts\ArgumentResolver>       $resolvers
     * @param list<class-string>                                                $scoped
     * @param list<class-string>                                                $notShared
     * @param list<class-string>                                                $notAutowired
     * @param \Popcorn\DI\Contracts\ArgumentResolver|null                       $defaultResolver
     */
    public function __construct(
        array             $bindings,
        array             $factories,
        array             $resolvers,
        array             $scoped,
        array             $notShared,
        array             $notAutowired,
        ?ArgumentResolver $defaultResolver = null
    )
    {
        $this->bindings        = $bindings;
        $this->factories       = $factories;
        $this->resolvers       = $resolvers;
        $this->scoped          = $scoped;
        $this->notShared       = $notShared;
        $this->notAutowired    = $notAutowired;
        $this->defaultResolver = $defaultResolver ?? new StandardArgumentResolver();
    }

    /**
     * Check if a service is registered in the container.
     *
     * @param class-string $service
     *
     * @return bool
     */
    public function has(string $service): bool
    {
        // The container can attempt to resolve this service if...
        return (
            // We already have an instance stored...
            isset($this->instances[$service])
            // There's a factory registered against it...
            || isset($this->factories[$service])
            // It's a registered alias, and its final class is valid...
            || ($this->isAlias($service) && $this->has($this->bindings[$service]))
            // Or it's a class that exists and isn't flagged as not to be autowired.
            || (class_exists($service) && ! in_array($service, $this->notAutowired, true))
        );
    }

    /**
     * Get a service from the container.
     *
     * @template TService of object
     *
     * @param class-string<TService> $service
     *
     * @return object
     *
     * @phpstan-return TService
     *
     * @throws \Popcorn\DI\Exceptions\ServiceNotFound Thrown if the service is not found in the container.
     * @throws \Popcorn\DI\Exceptions\ServiceContainerException Thrown if an error occurs during the resolution of the service.
     */
    public function get(string $service): object
    {
        // If it's an alias, we'll recurse and use the final class.
        if ($this->isAlias($service)) {
            // We wrap this here so that the exception that's thrown represents
            // the original service, not the alias.
            try {
                /**
                 * This is here to ensure that PHPStan understands the return type.
                 *
                 * @var TService
                 */
                return $this->get($this->bindings[$service]);
            } catch (Throwable $throwable) {
                throw ServiceContainerException::unresolvable($service, $throwable);
            }
        }

        // If the container doesn't support it, throw an exception.
        if (! $this->has($service)) {
            throw ServiceNotFound::make($service);
        }

        // If the service has already been resolved, we'll just that instance.
        if (isset($this->instances[$service])) {
            /**
             * This is here to ensure that PHPStan understands the return type.
             *
             * @var TService
             */
            return $this->instances[$service];
        }

        // If there's a registered factory for the service, we'll use that.
        if (isset($this->factories[$service])) {
            // We are intentionally not catching any exceptions here, as
            // they may have significance that we want to propagate.
            $instance = $this->factories[$service]->make($this);

            // If the instance is null, we'll throw an exception, as without it
            // the service is unresolvable.
            if ($instance === null) {
                throw ServiceContainerException::unresolvable($service);
            }
        } else {
            // If the service isn't autowired, and we don't already have an instance,
            // or a factory, we can't resolve, so can throw that PSR compliant exception.
            if (in_array($service, $this->notAutowired, true)) {
                throw ServiceNotFound::make($service);
            }

            // And finally, if we're here, we need to resolve the service using
            // autowiring.
            $instance = $this->resolve($service);

            // We don't check for null here because the resolve method doesn't
            // ever return null.
        }

        /** @var TService */
        return $instance;
    }

    /**
     * Resolve a callables dependencies and call it.
     *
     * @template TReturn of mixed|void
     *
     * @param callable(): TReturn $callable
     *
     * @return mixed
     *
     * @phpstan-return TReturn
     */
    public function call(callable $callable): mixed
    {
        // If it's not a closure, make it one.
        if (! $callable instanceof Closure) {
            $callable = $callable(...);
        }

        try {
            return $callable(...$this->collectArguments(new ReflectionFunction($callable)));
        } catch (ReflectionException $throwable) {
            throw ServiceContainerException::uncallable($throwable);
        }
    }

    /**
     * Resolve a methods' dependencies and call it.
     *
     * @param class-string|object $scope
     * @param string              $method
     *
     * @return mixed
     */
    public function callMethod(string|object $scope, string $method): mixed
    {
        $class = is_string($scope) ? $scope : $scope::class;

        try {
            $reflection = new ReflectionMethod($scope, $method);

            if ($reflection->isStatic()) {
                return $class::$method(...$this->collectArguments($reflection, $class));
            }

            if (! is_object($scope)) {
                $scope = $this->get($scope);
            }

            return $reflection->invokeArgs($scope, $this->collectArguments($reflection, $scope::class));
        } catch (ReflectionException $throwable) {
            throw ServiceContainerException::uncallableMethod(
                $class,
                $method,
                $throwable
            );
        }
    }

    /**
     * Check if a service is marked as being shared.
     *
     * @param class-string $service
     *
     * @return bool
     */
    public function isShared(string $service): bool
    {
        return ! in_array($service, $this->notShared, true);
    }

    /**
     * Check if a service is registered as an alias.
     *
     * @param class-string $service
     *
     * @return bool
     */
    public function isAlias(string $service): bool
    {
        return isset($this->bindings[$service]);
    }

    /**
     * Check if a service has been resolved.
     *
     * This method will return <code>true</code> if a service has already been
     * resolved, and <code>false</code> if it has not.
     *
     * Services that are not shared will always return <code>false</code>,
     * even if they have been resolved before.
     *
     * @param class-string $service
     *
     * @return bool
     */
    public function resolved(string $service): bool
    {
        return isset($this->instances[$service]);
    }

    /**
     * @template TService of object
     *
     * @param class-string<TService> $service
     *
     * @return object
     *
     * @phpstan-return TService
     *
     * @throws \Popcorn\DI\Exceptions\ServiceContainerException
     */
    private function resolve(string $service): object
    {
        try {
            $reflection = new ReflectionClass($service);

            // If the class isn't instantiable, we'll throw an exception
            // because it probably should have been bound to a concrete or
            // had a factory registered.
            if ($reflection->isInstantiable()) {
                throw ServiceContainerException::notInstantiable($service);
            }

            $constructor = $reflection->getConstructor();

            // If there's no constructor, or it has no parameters, we can just
            // create a new instance.
            if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
                return new $service();
            }

            // If we're here, it has parameters, so we'll collect the arguments
            // we need to instantiate and then do so.
            return $reflection->newInstanceArgs($this->collectArguments($constructor, $service));
        } catch (ReflectionException $e) {
            // This wraps the whole block, as both the creation of the
            // ReflectionClass and the instantiation with newInstanceArgs can
            // both throw exceptions.
            throw ServiceContainerException::unresolvable($service, $e);
        }
    }

    /**
     * @param \ReflectionFunctionAbstract $reflection
     * @param class-string|null           $service
     *
     * @return array<string, mixed>
     */
    private function collectArguments(ReflectionFunctionAbstract $reflection, ?string $service = null): array
    {
        $arguments = [];

        foreach ($reflection->getParameters() as $parameter) {
            // Resolve the parameter using a resolver and add it to the
            // argument stack.
            $arguments[$parameter->getName()] = $this->resolveParameter($parameter, $reflection->getName(), $service);
        }

        return $arguments;
    }

    /**
     * @param \ReflectionParameter $parameter
     * @param string               $function
     * @param class-string|null    $service
     *
     * @return mixed
     */
    private function resolveParameter(ReflectionParameter $parameter, string $function, ?string $service = null): mixed
    {
        // See if we have any registered resolvers that have attributes that
        // are present for the parameter.
        foreach ($this->resolvers as $attribute => $resolver) {
            if ($parameter->getAttributes($attribute, ReflectionAttribute::IS_INSTANCEOF)) {
                // If there is, resolve using that resolver.
                return $this->callResolver($resolver, $parameter, $function, $service);
            }
        }

        // If not, we'll fall back to the default one.
        return $this->callResolver($this->defaultResolver, $parameter, $function, $service);
    }

    /**
     * @param \Popcorn\DI\Contracts\ArgumentResolver $resolver
     * @param \ReflectionParameter                   $parameter
     * @param string                                 $function
     * @param class-string|null                      $service
     *
     * @return mixed
     */
    private function callResolver(ArgumentResolver $resolver, ReflectionParameter $parameter, string $function, ?string $service = null): mixed
    {
        // If the resolver is context-aware, we'll set the context to
        // the function and service.
        if ($resolver instanceof Contracts\ContextAwareArgumentResolver) {
            $resolver->setContext($function, $service);
        }

        $value = $resolver->resolve($parameter, $this);

        // If the resolver is context-aware, we'll flush the context
        // afterwards.
        if ($resolver instanceof Contracts\ContextAwareArgumentResolver) {
            $resolver->flushContext();
        }

        // We simply return whatever the resolver returned here. It's the
        // resolvers' job to ensure that the value is valid.
        return $value;
    }

    /**
     * Set an instance within the container.
     *
     * @template TService of object
     *
     * @param object                      $instance
     * @param class-string<TService>|null $service
     * @param bool                        $overwrite
     *
     * @phpstan-param TService            $instance
     *
     * @return static
     */
    public function instance(object $instance, ?string $service = null, bool $overwrite = false): static
    {
        // If the service name wasn't provided, we'll assume it's the class name.
        $service ??= $instance::class;

        // If we aren't overwriting, and the instance is already present,
        // it's time to throw an exception.
        if (isset($this->instances[$service]) && ! $overwrite) {
            throw ServiceContainerException::instanceAlreadySet($service);
        }

        // Otherwise, we just store the instance.
        $this->instances[$service] = $instance;

        return $this;
    }

    /**
     * Flush the current scope objects.
     *
     * @return static
     */
    public function flushScope(): static
    {
        foreach ($this->scoped as $class) {
            unset($this->instances[$class]);
        }

        return $this;
    }
}
