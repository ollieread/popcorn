<?php
declare(strict_types=1);

namespace Popcorn\DI\_Pre;

use Closure;
use Popcorn\DI\_Pre\Contracts\ServiceContainer;
use Popcorn\DI\_Pre\Contracts\ServiceResolver;
use Popcorn\DI\_Pre\Resolvers\StandardResolver;
use Popcorn\DI\Attributes\NoAutowiring;
use Popcorn\DI\Attributes\NotShared;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;
use RuntimeException;

final class Container implements ServiceContainer
{
    /**
     * The current context stack.
     *
     * @var \Popcorn\DI\_Pre\ContextStack
     */
    private ContextStack $context;

    /**
     * Class alias mappings.
     *
     * @var array<class-string, class-string>
     */
    private array $aliases;

    /**
     * The default resolver to use if no others are applicable.
     *
     * @var \Popcorn\DI\_Pre\Contracts\ServiceResolver
     */
    private ServiceResolver $defaultResolver;

    /**
     * Resolver mapping of attributes to handler.
     *
     * @var array<class-string, class-string<\Popcorn\DI\_Pre\Contracts\ServiceResolver>>
     */
    private array $resolverMapping;

    /**
     * Resolver instances mapped to their class.
     *
     * @var array<class-string<\Popcorn\DI\_Pre\Contracts\ServiceResolver>, \Popcorn\DI\_Pre\Contracts\ServiceResolver>
     */
    private array $resolvers = [];

    /**
     * Factory instances mapped to their class.
     *
     * @var array<class-string, \Popcorn\DI\_Pre\Contracts\ServiceFactory>
     */
    private array $factories;

    /**
     * Dependencies that have already been resolved.
     *
     * @var array<class-string, object>
     */
    private array $instances = [];

    /**
     * Classes whose instances should not be shared.
     *
     * @var list<class-string>
     */
    private array $notShared = [];

    /**
     * Classes that should not be autowired.
     *
     * @var list<class-string>
     */
    private array $noAutowiring = [
        ServiceContainer::class,
        Container::class,
        ContextStack::class,
    ];

    /**
     * @var list<class-string>
     */
    private array $resolutionStack = [];

    /**
     * @param array<class-string, class-string>                                             $aliases
     * @param array<class-string, class-string<\Popcorn\DI\_Pre\Contracts\ServiceResolver>> $resolvers
     * @param array<class-string, \Popcorn\DI\_Pre\Contracts\ServiceFactory>                $factories
     * @param \Popcorn\DI\_Pre\ContextStack|null                                            $context
     * @param \Popcorn\DI\_Pre\Contracts\ServiceResolver|null                               $defaultResolver
     */
    public function __construct(
        array            $aliases = [],
        array            $resolvers = [],
        array            $factories = [],
        ?ContextStack    $context = null,
        ?ServiceResolver $defaultResolver = null
    )
    {
        $this->aliases         = $aliases;
        $this->resolverMapping = $resolvers;
        $this->factories       = $factories;
        $this->context         = $context ?? new ContextStack();
        $this->defaultResolver = $defaultResolver ?? new StandardResolver();

        // Add an instance for the context stack, so it isn't manually resolved.
        $this->instances[ContextStack::class] = $this->context;
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
        return class_exists($service);
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
     * @throws \ReflectionException
     */
    public function get(string $service): object
    {
        // Always check if it's a context service first.
        if ($this->context->has($service)) {
            // If it is, return that.
            return $this->context->get($service);
        }

        if (isset($this->aliases[$service])) {
            /**
             * If it's an alias, we'll recurse this method call.
             *
             * @var TService
             */
            return $this->get($this->aliases[$service]);
        }

        if (isset($this->instances[$service])) {
            /** @var TService */
            return $this->instances[$service];
        }

        if ($this->shouldNotBeAutowired($service)) {
            throw new RuntimeException(sprintf('Class %s is not autowirable', $service));
        }

        if (isset($this->factories[$service])) {
            $instance = $this->resolveWithFactory($service);
        } else {
            $instance = $this->resolve($service);
        }

        /** @var TService $instance */

        if ($this->shouldBeShared($service)) {
            $this->instances[$service] = $instance;
        }

        return $instance;
    }

    /**
     * Resolve and call a callable.
     *
     * @template TReturn of mixed|void|never
     *
     * @param \Closure(): TReturn      $closure
     * @param class-string|object|null $scope
     *
     * @return mixed
     *
     * @phpstan-return TReturn
     *
     * @throws \ReflectionException
     */
    public function closure(Closure $closure, string|object|null $scope = null): mixed
    {
        try {
            $reflection = new ReflectionFunction($closure);
        } catch (ReflectionException $e) {
            throw new RuntimeException('Unable to determine callable dependencies', 0, $e);
        }

        $arguments = [];

        // If there are parameters, we should resolve them.
        if ($reflection->getNumberOfParameters() > 0) {
            $arguments = $this->collectArguments($reflection);
        }

        // If there's no scope, we can just call the closure directly.
        if ($scope === null) {
            return $closure(...$arguments);
        }

        // If we are here, there's a scope, so we bind the closure to it.
        $boundClosure = Closure::bind($closure, is_string($scope) ? null : $scope, $scope);

        // And then we simply call it.
        return $boundClosure(...$arguments);
    }

    /**
     * Call a method on a class.
     *
     * @param string              $method
     * @param class-string|object $class
     *
     * @return mixed
     */
    public function method(string $method, object|string $class): mixed
    {
        try {
            $reflection = new ReflectionMethod($class, $method);
        } catch (ReflectionException $e) {
            throw new RuntimeException(sprintf('Method %s not found in class %s', $method, $class), 0, $e);
        }

        return null;
    }

    private function shouldBeShared(string $class): bool
    {
        return ! in_array($class, $this->notShared, true);
    }

    private function shouldNotBeAutowired(string $class): bool
    {
        return in_array($class, $this->noAutowiring, true);
    }

    /**
     * Resolve a service using reflection.
     *
     * @template TService of object
     *
     * @param class-string<TService> $class
     *
     * @return object
     *
     * @phpstan-return TService
     *
     * @throws \ReflectionException
     */
    private function resolve(string $class): object
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw new RuntimeException(sprintf('Class %s not found', $class), 0, $e);
        }

        $this->performPreResolutionTasks($class, $reflection);

        $constructor = $reflection->getConstructor();

        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return new $class();
        }

        $arguments = $this->collectArguments($constructor);

        /** @var TService $instance */
        $instance = $reflection->newInstanceArgs($arguments);

        $this->performPostResolutionTasks($class, $instance, $reflection);

        return $instance;
    }

    /**
     * @param \ReflectionFunctionAbstract $reflection
     *
     * @return array<string, mixed>
     *
     * @throws \ReflectionException
     */
    private function collectArguments(ReflectionFunctionAbstract $reflection): array
    {
        $arguments = [];

        foreach ($reflection->getParameters() as $parameter) {
            $arguments[$parameter->getName()] = $this->resolveParameter($parameter);
        }

        return $arguments;
    }

    /**
     * Perform any relevant tasks before resolving a class.
     *
     * @template TClass of object
     *
     * @param class-string<TClass>     $class
     * @param \ReflectionClass<TClass> $reflection
     *
     * @return void
     */
    private function performPreResolutionTasks(string $class, ReflectionClass $reflection): void
    {
        // Make sure we're not in some sort of circular dependency.
        if (in_array($class, $this->resolutionStack, true)) {
            throw new RuntimeException(sprintf('Circular dependency detected for class %s', $class));
        }

        // Make sure that the class can actually be instantiated.
        if ($reflection->isInstantiable() === false) {
            throw new RuntimeException(sprintf('Class %s is not instantiable', $class));
        }

        // Check to see if we can actually autowire this class.
        if ($reflection->getAttributes(NoAutowiring::class)) {
            $this->noAutowiring[] = $class;
            throw new RuntimeException(sprintf('Class %s is not autowirable', $class));
        }

        // Add the class to the resolution stack.
        $this->resolutionStack[] = $class;
    }

    /**
     * Perform any relevant tasks after resolving a class.
     *
     * @template TClass of object
     *
     * @param class-string<TClass>     $class
     * @param object                   $instance
     * @param \ReflectionClass<TClass> $reflection
     *
     * @phpstan-param TClass           $instance
     *
     * @return void
     */
    private function performPostResolutionTasks(string $class, object $instance, ReflectionClass $reflection): void
    {
        // Check if the class is marked as "not-shared"
        if ($reflection->getAttributes(NotShared::class)) {
            // If it, add it to the list.
            $this->notShared[] = $class;
        }

        // Remove the class from the resolution stack.
        array_pop($this->resolutionStack);
    }

    /**
     * Resolve a parameter using the registered resolvers.
     *
     * @param \ReflectionParameter $parameter
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    private function resolveParameter(ReflectionParameter $parameter): mixed
    {
        foreach ($this->resolverMapping as $attribute => $resolver) {
            if ($parameter->getAttributes($attribute, ReflectionAttribute::IS_INSTANCEOF)) {
                return $this->getResolver($resolver)->resolve($this, $parameter);
            }
        }

        return $this->defaultResolver->resolve($this, $parameter);
    }

    /**
     * Get the service resolver instance.
     *
     * @template TResolver of \Popcorn\DI\_Pre\Contracts\ServiceResolver
     *
     * @param class-string<TResolver> $class
     *
     * @return \Popcorn\DI\_Pre\Contracts\ServiceResolver
     *
     * @phpstan-return ServiceResolver
     *
     * @throws \ReflectionException
     */
    private function getResolver(string $class): ServiceResolver
    {
        if (! isset($this->resolvers[$class])) {
            $this->resolvers[$class] = $this->resolve($class);
        }

        /** @var ServiceResolver */
        return $this->resolvers[$class];
    }

    /**
     * Resolver a service using a factory.
     *
     * @template TService of object
     *
     * @param class-string<TService> $class
     *
     * @return object
     *
     * @phpstan-return TService
     */
    private function resolveWithFactory(string $class): object
    {
        $factory = $this->factories[$class] ?? null;

        if ($factory === null) {
            throw new RuntimeException(sprintf('No factory found for class %s', $class));
        }

        return $factory->make($this);
    }
}
