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
    private(set) ServiceContainer $container;

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
     * The filepath for the cached files.
     *
     * @var string|null
     */
    private(set) ?string $cachePath = null;

    /**
     * @param list<class-string<\Popcorn\Core\Contracts\Bootstrapper>> $bootstrappers
     * @param string|null                                              $cachePath
     */
    public function __construct(
        array   $bootstrappers,
        ?string $cachePath = null,
    )
    {
        $this->bootstrappers = $bootstrappers;
        $this->cachePath     = $cachePath;
    }

    public function setRuntime(Runtime $runtime): self
    {
        $this->runtime = $runtime;

        return $this;
    }

    public function setServiceContainer(ServiceContainer $container): self
    {
        $this->container = $container;

        return $this;
    }


    /**
     * Boot the application.
     *
     * @return void
     */
    public function boot(): void
    {
        // Perform the bootstrapping of the core components.
        $this->bootstrap();

        // Make sure the runtime is aware of the popcorn instance.
        $this->runtime->setPopcorn($this);

        // Make sure the runtime is booted.
        $this->runtime->boot();
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
            new $bootstrapper()->bootstrap($this);
        }
    }

    /**
     * Run the application.
     *
     * @return void
     */
    public function run(): void
    {
        // Run the runtime, which could do any number of other things.
        $this->runtime->run();
    }
}
