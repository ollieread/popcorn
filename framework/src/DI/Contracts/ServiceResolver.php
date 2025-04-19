<?php

namespace Popcorn\DI\Contracts;

use ReflectionParameter;

interface ServiceResolver
{
    /**
     * Resolve a parameter.
     *
     * @param \Popcorn\DI\Contracts\ServiceContainer $container
     * @param \ReflectionParameter                   $parameter
     *
     * @return mixed
     */
    public function resolve(ServiceContainer $container, ReflectionParameter $parameter): mixed;
}
