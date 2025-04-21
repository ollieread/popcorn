<?php
declare(strict_types=1);

namespace Popcorn\Http;

use Popcorn\DI\Contracts\ServiceCollector;
use Popcorn\DI\Contracts\ServiceProvider;
use Popcorn\Http\Contracts\Request;

final class HttpServiceProvider implements ServiceProvider
{
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
        $this->registerRequest($collector);
    }

    private function registerRequest(ServiceCollector $collector): void
    {
        // The request class shouldn't be shared or autowired.
        $collector->notShared(Request::class);
        $collector->notAutowired(Request::class);

        // It should always be retrieved using its factory.
        $collector->factory(Request::class, new Factories\RequestServiceFactory());
    }
}
