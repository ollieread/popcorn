<?php

namespace Popcorn\DI\Contracts;

use ReflectionParameter;

interface ArgumentResolver
{
    /**
     * Resolve the argument for the given parameter.
     *
     * @param \ReflectionParameter                   $parameter
     * @param \Popcorn\DI\Contracts\ServiceContainer $container
     *
     * @return mixed
     */
    public function resolve(ReflectionParameter $parameter, ServiceContainer $container): mixed;
}
