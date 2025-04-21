<?php
declare(strict_types=1);

namespace Popcorn\Core\Bootstrappers;

use InvalidArgumentException;
use Popcorn\Core\Contracts\Bootstrapper;
use Popcorn\Core\EnvVars;
use Popcorn\Core\Popcorn;

/**
 * ENV Loader
 * ==========
 *
 * This is a base class for the different env loaders.
 *
 * @package Bootstrappers
 */
abstract class EnvLoader implements Bootstrapper
{
    private static string $envFilePath;

    public static function setFilePath(string $filePath): void
    {
        self::$envFilePath = realpath($filePath) ?: throw new InvalidArgumentException('Invalid file path: ' . $filePath);
    }

    protected static function getFilePath(string $default): string
    {
        return self::$envFilePath ?? $default;
    }

    /**
     * Perform the bootstrapping.
     *
     * This method is called during the application boot phase.
     *
     * @param \Popcorn\Core\Popcorn $popcorn
     *
     * @return void
     */
    public function bootstrap(Popcorn $popcorn): void
    {
        $variables = $this->loadVariables();

        /** @var array<string, mixed> $variables */

        // Set the instance of the container.
        $popcorn->container->instance(new EnvVars($variables), EnvVars::class, true);
    }

    /**
     * Load the variables.
     *
     * @return array<string, mixed>
     */
    abstract protected function loadVariables(): array;
}
