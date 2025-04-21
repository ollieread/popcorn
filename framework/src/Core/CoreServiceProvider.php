<?php
declare(strict_types=1);

namespace Popcorn\Core;

use Popcorn\Core\Contracts\Runtime;
use Popcorn\Core\Factories\RuntimeServiceFactory;
use Popcorn\DI\Contracts\ServiceCollector;
use Popcorn\DI\Contracts\ServiceProvider;

/**
 * Core Service Provider
 * =====================
 *
 * This service provider is responsible for registering the core services, with
 * their respective factories, bindings, etc.
 */
final class CoreServiceProvider implements ServiceProvider
{
    /**
     * @var \Popcorn\Core\Popcorn
     */
    private Popcorn $popcorn;

    public function __construct(Popcorn $popcorn)
    {
        $this->popcorn = $popcorn;
    }

    /**
     * Register services.
     *
     * This method is called when the {@see \Popcorn\DI\Contracts\ServiceContainer}
     * is being created.
     * It is used to register services, bindings, factories, etc.
     *
     * @param \Popcorn\DI\Contracts\ServiceCollector $collector
     *
     * @return void
     */
    public function register(ServiceCollector $collector): void
    {
        $this->registerRuntime($collector);
    }

    private function registerRuntime(ServiceCollector $collector): void
    {
        // The runtime class shouldn't be shared or autowired.
        $collector->notShared(Runtime::class);
        $collector->notAutowired(Runtime::class);

        // It should always be retrieved from the current Popcorn instance,
        // which is what this factory does.
        $collector->factory(Runtime::class, new RuntimeServiceFactory($this->popcorn));
    }
}
