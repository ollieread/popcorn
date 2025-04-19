<?php
declare(strict_types=1);

namespace Popcorn\Http;

use Popcorn\DI\Contracts\ServiceContainer;
use Popcorn\DI\Contracts\ServiceResolver;
use Popcorn\Http\Contracts\Request;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

final class ContextResolver implements ServiceResolver
{
    /**
     * Resolve a parameter.
     *
     * @param \Popcorn\DI\Contracts\ServiceContainer $container
     * @param \ReflectionParameter                   $parameter
     *
     * @return mixed
     */
    public function resolve(ServiceContainer $container, ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        // Make sure that the type is a valid class.
        if (
            ! $type instanceof ReflectionNamedType
            || $type->isBuiltin()
            || ! class_exists($className = $type->getName())
        ) {
            throw new RuntimeException('Invalid parameter type for context');
        }

        // Then get the context instance from the request.
        $instance = $container->get(Request::class)->context($className);

        if ($instance === null) {
            if ($parameter->allowsNull()) {
                return null;
            }

            throw new RuntimeException(sprintf('No context found for %s', $className));
        }

        return $instance;
    }
}
