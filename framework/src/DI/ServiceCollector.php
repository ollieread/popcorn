<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Popcorn\DI;


use Popcorn\DI\Contracts\ArgumentResolver;
use Popcorn\DI\Contracts\ServiceFactory;
use Popcorn\DI\Contracts\ServiceProvider;

final class ServiceCollector implements Contracts\ServiceCollector
{
    /**
     * The service providers that have been registered.
     *
     * @var list<class-string<\Popcorn\DI\Contracts\ServiceProvider>>
     */
    private(set) array $providers = [];

    /**
     * The bindings of abstracts to concretes.
     *
     * @var array<class-string, class-string>
     */
    private(set) array $bindings = [];

    /**
     * The service factory bindings.
     *
     * @var array<class-string, \Popcorn\DI\Contracts\ServiceFactory<object>>
     */
    private(set) array $factories = [];

    /**
     * Classes that aren't shared and should be resolved each time.
     *
     * @var list<class-string>
     */
    private(set) array $notShared = [];

    /**
     * Classes that aren't autowired, and either shouldn't be resolved at all
     * or should be resolved manually.
     *
     * @var list<class-string>
     */
    private(set) array $notAutowired = [];

    /**
     * The namespace to directory mappings for service discovery.
     *
     * @var array<string, list<string>>
     */
    private(set) array $namespaceMappings = [];

    /**
     * Files that should be excluded from service discovery.
     *
     * @var list<string>
     */
    private(set) array $excludeFiles = [];

    /**
     * The attribute to resolver mapping.
     *
     * @var array<class-string, \Popcorn\DI\Contracts\ArgumentResolver>
     */
    private(set) array $resolvers = [];

    /**
     * The scoped services.
     *
     * @var list<class-string>
     */
    private(set) array $scoped = [];

    /**
     * Collect services from a given service provider.
     *
     * @param \Popcorn\DI\Contracts\ServiceProvider $provider
     *
     * @return static
     */
    public function collect(ServiceProvider $provider): static
    {
        // Make sure we aren't double-registering providers
        if (! in_array($provider::class, $this->providers, true)) {
            // Register the provider.
            $provider->register($this);
            // Make sure the provider is flagged as being processed.
            $this->providers[] = $provider::class;
        }

        return $this;
    }

    /**
     * Create a binding between an abstract and its concrete.
     *
     * Bindings created this way are effectively aliases, and when
     * <code>$abstract</code> needs to be resolver, <code>$concrete</code>
     * will be resolved instead.
     *
     * If <code>$shared</code> is <code>false</code>, both the
     * <code>$abstract</code>, and <code>$concrete</code> will need to be
     * resolved each them they're
     * needed.
     *
     * @template TAbstract of object
     *
     * @param class-string<TAbstract> $abstract
     * @param class-string<TAbstract> $concrete
     * @param bool                    $shared
     *
     * @return static
     */
    public function bindAs(string $abstract, string $concrete, bool $shared = true): static
    {
        $this->bindings[$abstract] = $concrete;

        return $shared ? $this : $this->notShared($abstract, $concrete);
    }

    /**
     * Create a binding between a class and a factory.
     *
     * Factories are used to manually resolve instances of a class.
     * They are not needed for the majority of cases and only exist to
     * facilitate the resolution of complex objects.
     *
     * If <code>$shared</code> is <code>false</code>, the <code>$class</code>
     * will need to be resolved each time it's needed.
     *
     * @template TService of object
     *
     * @param class-string<TService>                         $class
     * @param \Popcorn\DI\Contracts\ServiceFactory<TService> $factory
     * @param bool                                           $shared
     *
     * @return static
     */
    public function factory(string $class, ServiceFactory $factory, bool $shared = true): static
    {
        $this->factories[$class] = $factory;

        return $shared ? $this : $this->notShared($class);
    }

    /**
     * Register one or more classes as not shared.
     *
     * Classes that are marked as 'not shared' will be resolved each time
     * they are needed, even if they are bound to a concrete class.
     *
     * @param class-string ...$classes
     *
     * @return static
     */
    public function notShared(string ...$classes): static
    {
        // The 'array_values' here is necessary because PHPStan complains,
        // although in reality it would always be a 'list'.
        $this->notShared = array_values(array_unique(array_merge($this->notShared, $classes)));

        return $this;
    }

    /**
     * Register one or more classes as not autowired.
     *
     * Classes that are marked as 'not autowired' will not be automatically
     * resolved by the container.
     * This is useful for classes that are always manually created.
     *
     * @param class-string ...$classes
     *
     * @return static
     */
    public function notAutowired(string ...$classes): static
    {
        // The 'array_values' here is necessary because PHPStan complains,
        // although in reality it would always be a 'list'.
        $this->notAutowired = array_values(array_unique(array_merge($this->notAutowired, $classes)));

        return $this;
    }

    /**
     * Map a namespace to a path for service discovery.
     *
     * During service discovery, the container will look for classes in the
     * provided path(s) that match the provided namespace.
     *
     * If there's already a mapping, and <code>$overwrite</code> is
     * <code>false</code>, the new path(s) will be appended, otherwise the
     * existing path mapping will be overwritten.
     *
     * @param string              $namespace
     * @param string|list<string> $path
     * @param bool                $overwrite
     *
     * @return static
     */
    public function discover(string $namespace, array|string $path, bool $overwrite = false): static
    {
        if ($overwrite || ! isset($this->namespaceMappings[$namespace])) {
            // If it's set to overwrite, or doesn't exist, we can just set it.
            $this->namespaceMappings[$namespace] = is_array($path) ? $path : [$path];
        } else {
            // If we are here, it exists, and we aren't overwriting, so we will
            // need to merge the new path(s) with the existing ones and remove
            // duplicates.
            $this->namespaceMappings[$namespace] = array_values(array_unique(
                array_merge($this->namespaceMappings[$namespace], is_array($path) ? $path : [$path])
            ));
        }

        return $this;
    }

    /**
     * Exclude file paths from service discovery.
     *
     * @param string ...$filePaths
     *
     * @return static
     */
    public function exclude(string ...$filePaths): static
    {
        // The 'array_values' here is necessary because PHPStan complains,
        // although in reality it would always be a 'list'.
        $this->excludeFiles = array_values(array_unique(array_merge($this->excludeFiles, $filePaths)));

        return $this;
    }

    /**
     * Register an attribute with an argument resolver.
     *
     * When collecting arguments for a service or method call, the container
     * will use the provided resolver to resolve any parameters that are
     * marked with the provided attribute.
     *
     * @param class-string                           $attribute
     * @param \Popcorn\DI\Contracts\ArgumentResolver $resolver
     *
     * @return static
     */
    public function resolver(string $attribute, ArgumentResolver $resolver): static
    {
        $this->resolvers[$attribute] = $resolver;

        return $this;
    }

    /**
     * Register a service as belonging to a scoped context.
     *
     * Scoped services are tied to the current context, and as such, exist
     * outside the normal resolution process.
     *
     * @param class-string ...$services
     *
     * @return static
     */
    public function scoped(string ...$services): static
    {
        $this->scoped[] = array_merge($this->scoped, $services);

        return $this;
    }
}
