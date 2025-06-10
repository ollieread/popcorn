<?php
declare(strict_types=1);

namespace Popcorn\Core;

use Closure;
use Popcorn\Core\Contracts\Runtime;
use Popcorn\DI\Contracts\ServiceContainer;
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
     *
     * @param string $path
     *
     * @return \Popcorn\Core\Contracts\Runtime
     */
    public static function loadRuntime(string $path): Runtime
    {
        // Load the runtime from the provided file path.
        $runtime = require $path;

        // If it's not a valid runtime, throw an exception.
        if (! $runtime instanceof Runtime) {
            throw new RuntimeException('Unable to load runtime');
        }

        return $runtime;
    }
}
