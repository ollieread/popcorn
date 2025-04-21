<?php

namespace Popcorn\DI\_Pre\Contracts;


use Closure;

interface ServiceContainer
{
    /**
     * Get a service from the container.
     *
     * @template TService of object
     *
     * @param class-string<TService> $service
     *
     * @return object
     *
     * @phpstan-return TService
     */
    public function get(string $service): object;

    /**
     * Resolve and call a closure.
     *
     * @template TReturn of mixed|void|never
     *
     * @param \Closure(): TReturn      $closure
     * @param class-string|object|null $scope
     *
     * @return mixed
     *
     * @phpstan-return TReturn
     */
    public function closure(Closure $closure, string|object|null $scope = null): mixed;

    /**
     * Call a method on a class.
     *
     * @param string              $method
     * @param class-string|object $class
     *
     * @return mixed
     */
    public function method(string $method, string|object $class): mixed;
}
