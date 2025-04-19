<?php
declare(strict_types=1);

namespace Popcorn\Http;

use Popcorn\DI\Attributes\NoAutowiring;
use Popcorn\DI\Attributes\NotShared;
use Popcorn\Http\Contracts\RequestContextRegistry;

#[NotShared, NoAutowiring]
class Request implements Contracts\Request
{
    private(set) readonly RequestMethod $method;

    private(set) RequestContextRegistry $context;

    public function __construct(
        RequestMethod           $method,
        ?RequestContextRegistry $context = null,
    )
    {
        $this->method  = $method;
        $this->context = $context ?? new RequestContextStack();
    }

    /**
     * Get a context object from the request.
     *
     * @template TContext of object
     *
     * @param class-string<TContext> $context
     *
     * @return object|null
     *
     * @phpstan-return TContext|null
     */
    public function context(string $context): ?object
    {
        if ($this->context->has($context) === false) {
            return null;
        }

        return $this->context->get($context, $this);
    }

    /**
     * Get the context object registry.
     *
     * @return \Popcorn\Http\Contracts\RequestContextRegistry
     */
    public function contextRegistry(): RequestContextRegistry
    {
        return $this->context;
    }

    /**
     * Get the request method.
     *
     * @return \Popcorn\Http\RequestMethod
     */
    public function method(): RequestMethod
    {
        return $this->method;
    }
}
