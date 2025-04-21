<?php

namespace Popcorn\DI\Contracts;

use Psr\Container\ContainerInterface;

interface ServiceContainer extends ContainerInterface
{
    /**
     * Check if a service can be resolved by the container.
     *
     * This method will return <code>true</code> if the container can attempt
     * to resolve the service, and <code>false</code> if it cannot.
     *
     * Returning <code>true</code> doesn't mean that the service will be
     * resolved successfully, just that the container can attempt it.
     *
     * @param class-string $service
     *
     * @return bool
     *
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public function has(string $service): bool;

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
     *
     * @throws \Popcorn\DI\Exceptions\ServiceNotFound Thrown if the service is not found in the container.
     * @throws \Popcorn\DI\Exceptions\ServiceContainerException Thrown if an error occurs during the resolution of the service.
     *
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public function get(string $service): object;

    /**
     * Resolve a callables dependencies and call it.
     *
     * @template TReturn of mixed|void
     *
     * @param callable(): TReturn $callable
     *
     * @return mixed
     *
     * @phpstan-return TReturn
     */
    public function call(callable $callable): mixed;

    /**
     * Resolve a methods' dependencies and call it.
     *
     * @param class-string|object $scope
     * @param string              $method
     *
     * @return mixed
     */
    public function callMethod(string|object $scope, string $method): mixed;

    /**
     * Check if a service is marked as being shared.
     *
     * @param class-string $service
     *
     * @return bool
     */
    public function isShared(string $service): bool;

    /**
     * Check if a service is registered as an alias.
     *
     * @param class-string $service
     *
     * @return bool
     */
    public function isAlias(string $service): bool;

    /**
     * Check if a service has been resolved.
     *
     * This method will return <code>true</code> if a service has already been
     * resolved, and <code>false</code> if it has not.
     *
     * Implementations may vary in how they determine whether a service has
     * been resolved or not.
     *
     * @param class-string $service
     *
     * @return bool
     */
    public function resolved(string $service): bool;

    /**
     * Set an instance within the container.
     *
     * @template TService of object
     *
     * @param object                      $instance
     * @param class-string<TService>|null $service
     *
     * @phpstan-param TService            $instance
     *
     * @return static
     */
    public function instance(object $instance, ?string $service = null, bool $overwrite = true): static;

    /**
     * Flush the current scope objects.
     *
     * @return static
     */
    public function flushScope(): static;
}
