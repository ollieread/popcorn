<?php

namespace Popcorn\DI\_Pre\Contracts;

use ReflectionParameter;

interface ServiceResolver
{
    /**
     * Resolve a parameter.
     *
     * @param \Popcorn\DI\_Pre\Contracts\ServiceContainer $container
     * @param \ReflectionParameter                        $parameter
     *
     * @return mixed
     */
    public function resolve(ServiceContainer $container, ReflectionParameter $parameter): mixed;
}
