<?php
declare(strict_types=1);

namespace Popcorn\Config;

final readonly class EnvVars
{
    /**
     * @var array<string, scalar>
     */
    private array $vars;

    /**
     * @param array<string, scalar> $vars
     */
    public function __construct(array $vars)
    {
        $this->vars = $vars;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->vars[$name] ?? $default;
    }
}
