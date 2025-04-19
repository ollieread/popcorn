<?php
declare(strict_types=1);

namespace Popcorn\Core\Bootstrappers;

use RuntimeException;
use Popcorn\Core\Contracts\Bootstrapper;
use Popcorn\Core\EnvVars;
use Popcorn\Core\Popcorn;
use Popcorn\DI\ContextStack;
use Throwable;

/**
 * Load Config Objects From Files
 * ==============================
 *
 * This class is responsible for loading the config objects from their respective
 * files and setting them within the context stack.
 *
 * @package Bootstrappers
 */
final class LoadConfigObjectsFromFiles implements Bootstrapper
{
    /**
     * The config class to file path mapping.
     *
     * @var array<class-string, string>
     */
    private static array $configMap = [];

    /**
     * @param array<class-string, string> $map
     *
     * @return void
     */
    public static function setConfigMap(array $map): void
    {
        self::$configMap = $map;
    }

    /**
     * @var \Popcorn\DI\ContextStack
     */
    private ContextStack $stack;

    public function __construct(ContextStack $stack)
    {
        $this->stack = $stack;
    }

    /**
     * Perform the bootstrapping.
     *
     * This method is called during the application boot phase.
     *
     * @param \Popcorn\Core\Popcorn $popcorn
     *
     * @return void
     *
     * @noinspection PhpUnusedLocalVariableInspection
     * @noinspection OnlyWritesOnParameterInspection
     */
    public function bootstrap(Popcorn $popcorn): void
    {
        // We need to make sure this variable is available here, as the
        // config files can use it.
        $env = $this->stack->get(EnvVars::class);

        // Create the config loader so that the config file only has access
        // to the env and its own file path.
        /** @phpstan-ignore closure.unusedUse */
        $configLoader = static function(string $filePath) use($env) {
            return require $filePath;
        };

        // Loop through the map and load each config file.
        foreach (self::$configMap as $class => $filePath) {
            // If the file doesn't exist, it's an error.
            if (! file_exists($filePath)) {
                throw new RuntimeException('Missing config file: ' . $filePath);
            }

            try {
                // Load the config using the config loader.
                $config = $configLoader($filePath);

                // If it's not the right class, that's an error too.
                if (! $config instanceof $class) {
                    throw new RuntimeException('Invalid config file: ' . $filePath);
                }

                // If we're here, it's all gone swimmingly, so we can set it.
                $this->stack->set($class, $config);
            } catch (Throwable $throwable) {
                throw new RuntimeException('Error loading config file: ' . $filePath, 0, $throwable);
            }
        }
    }
}
