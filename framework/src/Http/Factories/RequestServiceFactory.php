<?php
declare(strict_types=1);

namespace Popcorn\Http\Factories;

use Popcorn\Core\Contracts\Runtime;
use Popcorn\DI\Contracts\ServiceContainer;
use Popcorn\DI\Contracts\ServiceFactory;
use Popcorn\Http\HttpRuntime;
use RuntimeException;

/**
 * @implements \Popcorn\DI\Contracts\ServiceFactory<\Popcorn\Http\Contracts\Request>
 */
final class RequestServiceFactory implements ServiceFactory
{
    /**
     * Create a service instance.
     *
     * @param \Popcorn\DI\Contracts\ServiceContainer $container
     *
     * @return \Popcorn\Http\Contracts\Request
     */
    public function make(ServiceContainer $container): object
    {
        $runtime = $container->get(Runtime::class);

        if (! $runtime instanceof HttpRuntime) {
            throw new RuntimeException('Cannot access the request object outside the HTTP context');
        }

        return $runtime->request;
    }
}
