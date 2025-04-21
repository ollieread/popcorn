<?php
declare(strict_types=1);

namespace Popcorn\DI\_Pre\Resolvers;

use Popcorn\DI\_Pre\Contracts\ServiceContainer;
use Popcorn\DI\_Pre\Contracts\ServiceResolver;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

final class StandardResolver implements ServiceResolver
{
    /**
     * Resolve a parameter.
     *
     * @param \Popcorn\DI\_Pre\Contracts\ServiceContainer $container
     * @param \ReflectionParameter                        $parameter
     *
     * @return mixed
     */
    public function resolve(ServiceContainer $container, ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        // It's a named type, which realistically they all should be.
        if ($type instanceof ReflectionNamedType) {
            $typeName = $type->getName();

            // Is it a class?
            if (class_exists($typeName)) {
                // It is, so we resolve it using the service container we're in.
                return $container->get($typeName);
            }

            // Does it have a default value?
            if ($parameter->isDefaultValueAvailable()) {
                // If we're unable to resolve it, and it has a default,
                // we'll use that.
                return $parameter->getDefaultValue();
            }

            if ($parameter->allowsNull()) {
                // If the parameter allows null, we can return null.
                return null;
            }
        }

        // If we reach this point, we have no idea what to do.
        // This is a failure, so we throw an exception.
        throw new RuntimeException(sprintf(
            'Unable to resolve parameter %s in %s',
            $parameter->getName(),
            $parameter->getDeclaringClass()?->getName() ?? 'Closure'
        ));
    }
}
