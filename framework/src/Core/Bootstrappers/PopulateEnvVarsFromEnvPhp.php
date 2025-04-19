<?php
declare(strict_types=1);

namespace Popcorn\Core\Bootstrappers;

use InvalidArgumentException;

/**
 * Populate environment variables from env.php
 * =========================================
 *
 * This class is responsible for loading environment variables from a PHP file
 * named <code>.env.php</code>, rather than the traditional <code>.env</code> file.
 *
 * @package Bootstrappers
 */
final class PopulateEnvVarsFromEnvPhp extends EnvLoader
{
    /**
     * Load the variables.
     *
     * @return array<string, mixed>
     */
    protected function loadVariables(): array
    {
        $filePath = self::getFilePath(getcwd() . '/.env.php');

        if (! file_exists($filePath)) {
            throw new InvalidArgumentException('Missing env.php file: ' . $filePath);
        }

        $variables = require $filePath;

        if (! is_array($variables) || array_is_list($variables)) {
            throw new InvalidArgumentException('Invalid env.php file: ' . $filePath);
        }

        /** @var array<string, mixed> $variables */

        return $variables;
    }
}
