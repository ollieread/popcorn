<?php
declare(strict_types=1);

namespace Popcorn\Core\Factories;

use Popcorn\Core\Popcorn;
use Popcorn\DI\Contracts\ServiceContainer;
use Popcorn\DI\Contracts\ServiceFactory;

/**
 * @implements \Popcorn\DI\Contracts\ServiceFactory<\Popcorn\Core\Contracts\Runtime>
 */
final class RuntimeServiceFactory implements ServiceFactory
{
    /**
     * @var \Popcorn\Core\Popcorn
     */
    private Popcorn $popcorn;

    public function __construct(Popcorn $popcorn)
    {
        $this->popcorn = $popcorn;
    }

    /**
     * Create a service instance.
     *
     * @param \Popcorn\DI\Contracts\ServiceContainer $container
     *
     * @return \Popcorn\Core\Contracts\Runtime
     */
    public function make(ServiceContainer $container): object
    {
        return $this->popcorn->runtime;
    }
}
