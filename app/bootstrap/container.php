<?php
declare(strict_types=1);

use Popcorn\DI\Container;

return new Container(
/**
 * Add your alias mappings here, used to provide the target class for a
 * dependency when requested.
 * Typically used to map interfaces to implementations.
 *
 * <code>
 *     MyInterface::class => MyImplementation::class
 * </code>
 */
    aliases        : [
        ...Popcorn\Core\FrameworkHelper::defaultAliases(),
    ],
    /**
     * Add your resolver mappings here, mapping attributes to their handlers, to
     * allow for non-standard dependency resolution.
     *
     * Resolvers must implement <code>Popcorn\DI\Contracts\ServiceResolver</code>.
     *
     * <code>
     *     MyAttribute::class => MyResolver<Popcorn\DI\Contracts\ServiceResolver>::class
     * </code>
     */
    resolvers      : [
        // Allows for request context to be injected.
        ...Popcorn\Core\FrameworkHelper::defaultResolvers(),
    ],
    /**
     * Add your custom factories here, mapping classes to service factories
     * that are responsible for resolving them.
     *
     * Factories must implement <code>Popcorn\DI\Contracts\ServiceFactory</code>.
     *
     * <code>
     *     MyClass::class => new MyClassFactory
     * </code>
     */
    factories      : [
        ...Popcorn\Core\FrameworkHelper::defaultFactories(),
    ],
    /**
     * The default resolver to be used if the dependency being resolved is not
     * applicable to one of the above-defined resolvers.
     * This is optional, as the container itself will use an instance of
     * <code>StandardResolver</code> if none is provided.
     */
    defaultResolver: new Popcorn\DI\Resolvers\StandardResolver()
);
