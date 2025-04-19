<?php
declare(strict_types=1);

namespace Popcorn\Http;

use Popcorn\DI\Attributes\NoAutowiring;
use Popcorn\DI\Attributes\NotShared;
use Popcorn\Http\Contracts\Request;
use Popcorn\Http\Contracts\RequestContextRegistry;
use RuntimeException;

#[NoAutowiring, NotShared]
final class RequestContextStack implements RequestContextRegistry
{
    /**
     * @var array<class-string, object>
     */
    private array $stack = [];

    /**
     * @var array<class-string, callable(\Popcorn\Http\Contracts\Request): object>
     */
    private array $resolvers = [];

    /**
     * @template TContext of object
     *
     * @param class-string<TContext>                              $class
     * @param callable(\Popcorn\Http\Contracts\Request): TContext $resolver
     *
     * @return self
     */
    public function register(string $class, callable $resolver): self
    {
        if (isset($this->stack[$class])) {
            unset($this->stack[$class]);
        }

        $this->resolvers[$class] = $resolver;

        return $this;
    }

    /**
     * @param class-string $class
     *
     * @return bool
     */
    public function has(string $class): bool
    {
        return isset($this->resolvers[$class]);
    }

    /**
     * @template TContext of object
     *
     * @param class-string<TContext>          $class
     * @param \Popcorn\Http\Contracts\Request $request
     *
     * @return object
     *
     * @phpstan-return TContext
     */
    public function get(string $class, Request $request): object
    {
        if (isset($this->stack[$class])) {
            /** @var TContext */
            return $this->stack[$class];
        }

        if (! isset($this->resolvers[$class])) {
            throw new RuntimeException(sprintf('No context resolver registered for class %s', $class));
        }

        /** @var TContext */
        return $this->stack[$class] = ($this->resolvers[$class])($request);
    }
}
