<?php
declare(strict_types=1);

namespace Popcorn\Core;

use Closure;
use Popcorn\Core\Contracts\Runtime;
use Popcorn\DI\Contracts\ServiceContainer;
use Popcorn\Http\Attributes\Context;
use Popcorn\Http\ContextResolver;
use Popcorn\Http\Contracts\RequestContextRegistry;
use Popcorn\Http\RequestContextStack;
use RuntimeException;

final class FrameworkHelper
{
    /**
     * @param string $cachedPath
     *
     * @return \Closure(): ?\Popcorn\DI\Contracts\ServiceContainer
     */
    public static function containerLoader(string $cachedPath): Closure
    {
        return static function () use ($cachedPath): ?ServiceContainer {
            if (file_exists($cachedPath)) {
                $container = require $cachedPath;

                /** @var \Popcorn\DI\Contracts\ServiceContainer $container */
                return $container;
            }

            return null;
        };
    }

    /**
     * Load a runtime from a file.
     *
     * This method will attempt to load a runtime by requiring the provided
     * file.
     * If the return value is a <code>callable</code>, the service container
     * will be used to invoke it.
     *
     * @param string                                 $path
     * @param \Popcorn\DI\Contracts\ServiceContainer $container
     *
     * @return \Popcorn\Core\Contracts\Runtime
     */
    public static function loadRuntime(string $path, ServiceContainer $container): Runtime
    {
        /**
         * Load the runtime from the provided file path.
         *
         * @var \Popcorn\Core\Contracts\Runtime|callable(): \Popcorn\Core\Contracts\Runtime $runtime
         */
        $runtime = require $path;

        // If it's a callable, we'll use the container.
        if (is_callable($runtime)) {
            $runtime = $container->call($runtime);
        }

        // If it's not a valid runtime, throw an exception.
        if (! $runtime instanceof Runtime) {
            throw new RuntimeException('Unable to load runtime');
        }

        return $runtime;
    }

    /**
     * @return array<class-string, class-string>
     */
    public static function defaultAliases(): array
    {
        return [
            RequestContextRegistry::class => RequestContextStack::class,
        ];
    }

    /**
     * @return array<class-string, class-string<\Popcorn\DI\_Pre\Contracts\ServiceResolver>>
     */
    public static function defaultResolvers(): array
    {
        return [
            Context::class => ContextResolver::class,
        ];
    }

    /**
     * @return array<class-string, \Popcorn\DI\_Pre\Contracts\ServiceFactory<*>>
     */
    public static function defaultFactories(): array
    {
        return [];
    }
}
