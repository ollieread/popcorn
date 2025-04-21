<?php
declare(strict_types=1);

namespace Popcorn\Core\Bootstrappers;

use Popcorn\Core\Contracts\Bootstrapper;
use Popcorn\Core\Popcorn;
use Popcorn\DI\Contracts\ServiceContainer as ServiceContainerContract;
use Popcorn\DI\ServiceCollector;
use Popcorn\DI\ServiceContainer;
use RuntimeException;

/**
 *
 */
final class LoadAndConfigureServiceContainer implements Bootstrapper
{
    /**
     * @var (callable(\Popcorn\Core\Popcorn): ?\Popcorn\DI\Contracts\ServiceContainer)|null
     */
    private static $loader;

    /**
     * @param callable(\Popcorn\Core\Popcorn): ?\Popcorn\DI\Contracts\ServiceContainer $loader
     *
     * @return void
     */
    public static function setLoader(callable $loader): void
    {
        self::$loader = $loader;
    }

    /**
     * @var list<class-string<\Popcorn\DI\Contracts\ServiceProvider>>
     */
    private static array $providers = [];

    /**
     * @param list<class-string<\Popcorn\DI\Contracts\ServiceProvider>> $providers
     *
     * @return void
     */
    public static function setProviders(array $providers): void
    {
        self::$providers = $providers;
    }

    /**
     * @param list<class-string<\Popcorn\DI\Contracts\ServiceProvider>> $providers
     *
     * @return void
     */
    public static function addProviders(array $providers): void
    {
        self::$providers = array_values(array_unique(array_merge(self::$providers, $providers)));
    }

    /**
     * Perform the bootstrapping.
     *
     * This method is called during the application boot phase.
     *
     * @param \Popcorn\Core\Popcorn $popcorn
     *
     * @return void
     */
    public function bootstrap(Popcorn $popcorn): void
    {
        // Collect the possible container loaders in priority order.
        $loaders = [
            $this->loadUsingCustomLoader(...),
            $this->loadUsingCache(...),
            $this->loadUsingServiceProviders(...),
        ];

        // Cycle through them until we find one that works, which should always
        // at least be the last one.
        foreach ($loaders as $loader) {
            $container = $loader($popcorn);

            if ($container !== null) {
                $popcorn->setServiceContainer($container);
                return;
            }
        }

        throw new RuntimeException('Unable to load service container.');
    }

    /**
     * Attempt to load the service container using a custom loader, if one
     * was provided.
     *
     * @param \Popcorn\Core\Popcorn $popcorn
     *
     * @return \Popcorn\DI\Contracts\ServiceContainer|null
     */
    private function loadUsingCustomLoader(Popcorn $popcorn): ?ServiceContainerContract
    {
        if (self::$loader !== null) {
            return (self::$loader)($popcorn);
        }

        return null;
    }

    /**
     * Attempt to load the service container from the cache if it's present,
     * and the cache is being used.
     *
     * @param \Popcorn\Core\Popcorn $popcorn
     *
     * @return \Popcorn\DI\Contracts\ServiceContainer|null
     */
    private function loadUsingCache(Popcorn $popcorn): ?ServiceContainerContract
    {
        if ($popcorn->cachePath !== null) {
            $cachedContainer = $popcorn->cachePath . '/cached-container.php';

            if (! file_exists($cachedContainer)) {
                return null;
            }

            $container = require $cachedContainer;

            if (! $container instanceof ServiceContainerContract) {
                throw new RuntimeException('Cached container is not a valid instance of ServiceContainer.');
            }

            return $container;
        }

        return null;
    }

    /**
     * Load the service container using the service collector and the registered
     * service providers.
     *
     * @param \Popcorn\Core\Popcorn $popcorn
     *
     * @return \Popcorn\DI\Contracts\ServiceContainer
     */
    private function loadUsingServiceProviders(Popcorn $popcorn): ServiceContainerContract
    {
        // Create the service collector.
        $collector = new ServiceCollector();

        // Cycle through the registered providers and collect their services.
        foreach (self::$providers as $provider) {
            $collector->collect(new $provider($popcorn));
        }

        return new ServiceContainer(
            $collector->bindings,
            $collector->factories,
            $collector->resolvers,
            $collector->scoped,
            $collector->notShared,
            $collector->notAutowired,
        );
    }
}
