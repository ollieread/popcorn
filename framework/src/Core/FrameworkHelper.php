<?php
declare(strict_types=1);

namespace Popcorn\Core;

use Closure;
use Popcorn\Core\Contracts\Runtime;
use Popcorn\DI\Contracts\ServiceContainer;
use Popcorn\Http\Attributes\Context;
use Popcorn\Http\ContextResolver;
use Popcorn\Http\Contracts\RequestContextRegistry;
use Popcorn\Http\Factories\RequestContextRegistryFactory;
use Popcorn\Http\RequestContextStack;
use RuntimeException;

final class FrameworkHelper
{
    /**
     * @param string $cachedPath
     * @param string $defaultPath
     *
     * @return \Closure(): \Popcorn\DI\Contracts\ServiceContainer
     */
    public static function containerLoader(string $cachedPath, string $defaultPath): Closure
    {
        return static function () use ($cachedPath, $defaultPath): ServiceContainer {
            if (file_exists($cachedPath)) {
                $container = require $cachedPath;
            } else {
                $container = require $defaultPath;
            }

            /** @var \Popcorn\DI\Contracts\ServiceContainer $container */
            return $container;
        };
    }

    /**
     * @param string $path
     *
     * @return \Closure(\Popcorn\DI\Contracts\ServiceContainer): \Popcorn\Core\Contracts\Runtime
     */
    public static function runtimeLoader(string $path): Closure
    {
        return static function (ServiceContainer $container) use ($path): Runtime {
            /**
             * Load the runtime from the provided file path.
             *
             * @var \Popcorn\Core\Contracts\Runtime|callable(): \Popcorn\Core\Contracts\Runtime $runtime
             */
            $runtime = require $path;

            if (is_callable($runtime)) {
                $runtime = $container->closure($runtime);
            }

            if (! $runtime instanceof Runtime) {
                throw new RuntimeException('Unable to load runtime');
            }

            return $runtime;
        };
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
     * @return array<class-string, class-string<\Popcorn\DI\Contracts\ServiceResolver>>
     */
    public static function defaultResolvers(): array
    {
        return [
            Context::class => ContextResolver::class,
        ];
    }

    /**
     * @return array<class-string, \Popcorn\DI\Contracts\ServiceFactory<*>>
     */
    public static function defaultFactories(): array
    {
        return [];
    }
}
