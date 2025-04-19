<?php
/** @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection */
declare(strict_types=1);

namespace Popcorn\Core;

use RuntimeException;

final class EnvVars
{
    /**
     * The environment variables.
     *
     * @var array<string, mixed>
     */
    private array $variables;

    /**
     * @param array<string, mixed> $variables
     */
    public function __construct(array $variables)
    {
        $this->variables = $variables;
    }

    /**
     * Check if an environment variable exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        // This is using array_key_exists instead of isset because we want to
        // allow null values.
        return array_key_exists($name, $this->variables);
    }

    /**
     * Get an environment variable by its name.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get(string $name, mixed $default = null): mixed
    {
        // This is using array_key_exists instead of isset because we want to
        // allow null values.
        if (! array_key_exists($name, $this->variables)) {
            return $default;
        }

        return $this->variables[$name];
    }

    /**
     * Check if an environment variable is null.
     *
     * @param string $name
     *
     * @return bool
     */
    public function null(string $name): bool
    {
        $value = $this->get($name);

        return $value === null;
    }

    /**
     * Get an environment variable as a string.
     *
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public function string(string $name, string $default): string
    {
        $value = $this->get($name, $default) ?? $default;

        if (! is_string($value)) {
            throw new RuntimeException(sprintf('Environment variable "%s" is not a string.', $name));
        }

        return $value;
    }

    /**
     * Get an environment variable as an integer.
     *
     * @param string $name
     * @param int    $default
     *
     * @return int
     */
    public function int(string $name, int $default): int
    {
        $value = $this->get($name) ?? $default;

        if (! is_int($value)) {
            throw new RuntimeException(sprintf('Environment variable "%s" is not an integer.', $name));
        }

        return $value;
    }

    /**
     * Get an environment variable as a boolean.
     *
     * @param string $name
     * @param bool   $default
     *
     * @return bool
     */
    public function bool(string $name, bool $default): bool
    {
        $value = $this->get($name) ?? $default;

        if (! is_bool($value)) {
            throw new RuntimeException(sprintf('Environment variable "%s" is not a boolean.', $name));
        }

        return $value;
    }

    /**
     * Get an environment variable as a float.
     *
     * @param string $name
     * @param float  $default
     *
     * @return float
     */
    public function float(string $name, float $default): float
    {
        $value = $this->get($name) ?? $default;

        if (! is_float($value)) {
            throw new RuntimeException(sprintf('Environment variable "%s" is not a float.', $name));
        }

        return $value;
    }

    /**
     * Get an environment variable as a keyed array.
     *
     * @param string                  $name
     * @param array<array-key, mixed> $default
     *
     * @return array<array-key, mixed>
     */
    public function array(string $name, array $default): array
    {
        $value = $this->get($name) ?? $default;

        if (! is_array($value)) {
            throw new RuntimeException(sprintf('Environment variable "%s" is not an array.', $name));
        }

        return $value;
    }

    /**
     * Get an environment variable as a list.
     *
     * @param string      $name
     * @param list<mixed> $default
     *
     * @return list<mixed>
     */
    public function list(string $name, array $default): array
    {
        $value = $this->get($name) ?? $default;

        if (! is_array($value) || ! array_is_list($value)) {
            throw new RuntimeException(sprintf('Environment variable "%s" is not a list.', $name));
        }

        return $value;
    }
}
