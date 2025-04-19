<?php

namespace Popcorn\Http\Contracts;

use Popcorn\Http\RequestMethod;

interface Request
{
    /**
     * Get the request method.
     *
     * @return \Popcorn\Http\RequestMethod
     */
    public function method(): RequestMethod;

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
    public function context(string $context): ?object;

    /**
     * Get the context object registry.
     *
     * @return \Popcorn\Http\Contracts\RequestContextRegistry
     */
    public function contextRegistry(): RequestContextRegistry;
}
