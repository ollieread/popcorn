<?php

namespace Popcorn\Container\Contracts;

/**
 * Container
 * =========
 *
 * The container is responsible for managing the lifecycle of services and
 * dependencies.
 *
 * @package Container
 */
interface Container extends Factory
{
    /**
     * Get the container's factory.
     *
     * @return \Popcorn\Container\Contracts\Factory
     */
    public function factory(): Factory;
}
