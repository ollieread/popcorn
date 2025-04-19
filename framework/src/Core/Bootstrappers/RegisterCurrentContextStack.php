<?php
declare(strict_types=1);

namespace Popcorn\Core\Bootstrappers;

use Popcorn\Core\Contracts\Bootstrapper;
use Popcorn\Core\Contracts\Runtime;
use Popcorn\Core\Popcorn;
use Popcorn\DI\ContextStack;
use Popcorn\DI\Contracts\ServiceContainer;

/**
 * Register the default context
 * ============================
 *
 * This class is responsible for bootstrapping the context stack, so both the
 * current service container and runtime are available for injection.
 *
 * @package Bootstrappers
 */
final class RegisterCurrentContextStack implements Bootstrapper
{
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
     */
    public function bootstrap(Popcorn $popcorn): void
    {
        // Register the current service container.
        $this->stack->set(ServiceContainer::class, $popcorn->services);

        // And the current runtime.
        $this->stack->set(Runtime::class, $popcorn->runtime);
    }
}
