<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Popcorn\Http;

use Popcorn\Core\Contracts\Runtime;
use Popcorn\DI\_Pre\ContextStack;
use Popcorn\DI\_Pre\Contracts\ServiceContainer;
use Popcorn\DI\Attributes\NoAutowiring;
use Popcorn\DI\Attributes\NotShared;
use Popcorn\Http\Contracts\Request;
use Popcorn\Http\Contracts\RequestContextRegistry;
use RuntimeException;

#[NotShared, NoAutowiring]
final class HttpRuntime implements Runtime
{
    private(set) ServiceContainer $container;

    private(set) Request $request;

    /**
     * Set the service container for use in the runtime.
     *
     * @param \Popcorn\DI\_Pre\Contracts\ServiceContainer $container
     *
     * @return static
     */
    public function setServiceContainer(ServiceContainer $container): static
    {
        $this->container = $container;

        return $this;
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function boot(): void
    {
        $context = $this->container->get(ContextStack::class);

        // Register the request object, and the context registry with the context
        // stack, so they can be easily injected.
        $context->set(Request::class, $this->request);
        $context->set(RequestContextRegistry::class, $this->request->contextRegistry());
    }

    /**
     * Run the runtime.
     *
     * @return void
     */
    public function run(): void
    {
        if (! isset($this->request)) {
            throw new RuntimeException('Request is not set.');
        }

        var_dump('Hi from the HTTP runtime');
    }
}
