<?php

namespace Popcorn\DI\Contracts;

interface ContextAwareArgumentResolver
{
    /**
     * Set the current context.
     *
     * @param string            $function
     * @param class-string|null $service
     *
     * @return void
     */
    public function setContext(string $function, ?string $service = null): void;

    /**
     * Flush the current context.
     *
     * @return void
     */
    public function flushContext(): void;
}
