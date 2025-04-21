<?php

namespace Popcorn\DI\Contracts;

interface ServiceCollector
{
    /**
     * Collect services from a given service provider.
     *
     * @param \Popcorn\DI\Contracts\ServiceProvider $provider
     *
     * @return static
     */
    public function collect(ServiceProvider $provider): static;

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
    public function bindAs(string $abstract, string $concrete, bool $shared = true): static;

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
    public function factory(string $class, ServiceFactory $factory, bool $shared = true): static;

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
    public function notShared(string ...$classes): static;

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
    public function notAutowired(string ...$classes): static;

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
    public function discover(string $namespace, string|array $path, bool $overwrite = false): static;

    /**
     * Exclude file paths from service discovery.
     *
     * @param string ...$filePaths
     *
     * @return static
     */
    public function exclude(string ...$filePaths): static;

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
    public function resolver(string $attribute, ArgumentResolver $resolver): static;

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
    public function scoped(string ...$services): static;
}
