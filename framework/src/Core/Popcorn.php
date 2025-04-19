<?php
declare(strict_types=1);

namespace Popcorn\Core;

use Popcorn\Core\Contracts\Runtime;
use Popcorn\DI\Contracts\ServiceContainer;

final class Popcorn
{
    public static function builder(): PopcornBuilder
    {
        return new PopcornBuilder();
    }

    /**
     * The service container.
     *
     * @var \Popcorn\DI\Contracts\ServiceContainer
     */
    private(set) ServiceContainer $services;

    /**
     * The current instances runtime.
     *
     * @var \Popcorn\Core\Contracts\Runtime
     */
    private(set) Runtime $runtime;

    /**
     * @var list<class-string<\Popcorn\Core\Contracts\Bootstrapper>>
     */
    private(set) array $bootstrappers;

    /**
     * @param \Popcorn\DI\Contracts\ServiceContainer                   $services
     * @param \Popcorn\Core\Contracts\Runtime                          $runtime
     * @param list<class-string<\Popcorn\Core\Contracts\Bootstrapper>> $bootstrappers
     */
    public function __construct(
        ServiceContainer $services,
        Runtime          $runtime,
        array            $bootstrappers
    )
    {
        $this->services      = $services;
        $this->runtime       = $runtime;
        $this->bootstrappers = $bootstrappers;
    }

    /**
     * Boot the application.
     *
     * @return void
     */
    public function boot(): void
    {
        // Set the service container on the runtime.
        $this->runtime->setServiceContainer($this->services);

        // Perform the bootstrapping of the core components.
        $this->bootstrap();
    }

    /**
     * Run the bootstrappers.
     *
     * @return void
     */
    private function bootstrap(): void
    {
        // Loop through the bootstrappers.
        foreach ($this->bootstrappers as $bootstrapper) {
            // Get them from the container and bootstrap them.
            // We don't care about the instance, they're throw-away objects.
            $this->services->get($bootstrapper)->bootstrap($this);
        }
    }

    /**
     * Run the application.
     *
     * @return void
     */
    public function run(): void
    {
        // Boot the application.
        $this->boot();

        // Run the runtime, which could do any number of other things.
        $this->runtime->run();
    }
}
