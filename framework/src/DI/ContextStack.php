<?php
declare(strict_types=1);

namespace Popcorn\DI;

use Popcorn\DI\Attributes\NoAutowiring;
use RuntimeException;

#[NoAutowiring]
final class ContextStack
{
    /**
     * @var array<class-string, object>
     */
    private array $stack = [];

    /**
     * Get a context object for its core class name.
     *
     * @template TContext of object
     *
     * @param class-string<TContext> $class
     *
     * @return object
     *
     * @phpstan-return TContext
     */
    public function get(string $class): object
    {
        if (! isset($this->stack[$class])) {
            throw new RuntimeException('The context is not set for class: ' . $class);
        }

        /** @var TContext */
        return $this->stack[$class];
    }

    /**
     * Set a context object by its core class name.
     *
     * @template TContext of object
     *
     * @param class-string<TContext> $class
     * @param object                 $instance
     *
     * @phpstan-param TContext       $instance
     *
     * @return self
     */
    public function set(string $class, object $instance): self
    {
        if (isset($this->stack[$class])) {
            throw new RuntimeException('Context already set for class: ' . $class);
        }

        if (! $instance instanceof $class) {
            throw new RuntimeException('Instance is not of the expected class: ' . $class);
        }

        $this->stack[$class] = $instance;

        return $this;
    }

    /**
     * Check if a context object is present.
     *
     * @param class-string $class
     *
     * @return bool
     */
    public function has(string $class): bool
    {
        return isset($this->stack[$class]);
    }

    /**
     * Reset the context stack, removing all current context.
     *
     * @return self
     */
    public function reset(): self
    {
        $this->stack = [];

        return $this;
    }

    /**
     * Forget a context object by its core class name.
     *
     * @param class-string $class
     *
     * @return bool
     */
    public function forget(string $class): bool
    {
        if (isset($this->stack[$class])) {
            unset($this->stack[$class]);

            return true;
        }

        return false;
    }
}
