<?php

namespace Popcorn\Http\Contracts;

interface RequestContextRegistry
{
    /**
     * Get a context object using the request.
     *
     * @template TContext of object
     *
     * @param class-string<TContext>          $class
     * @param \Popcorn\Http\Contracts\Request $request
     *
     * @return object
     *
     * @phpstan-return TContext
     */
    public function get(string $class, Request $request): object;

    /**
     * Check if a context object is registered.
     *
     * @param class-string $class
     *
     * @return bool
     */
    public function has(string $class): bool;

    /**
     * Register a context object with a resolver.
     *
     * @template TContext of object
     *
     * @param class-string<TContext>                              $class
     * @param callable(\Popcorn\Http\Contracts\Request): TContext $resolver
     *
     * @return self
     */
    public function register(string $class, callable $resolver): self;
}
