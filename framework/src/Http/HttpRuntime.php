<?php
/** @noinspection PhpUnnecessaryStaticReferenceInspection */
declare(strict_types=1);

namespace Popcorn\Http;

use Popcorn\Core\Contracts\Runtime;
use Popcorn\Core\Popcorn;
use Popcorn\DI\Attributes\NoAutowiring;
use Popcorn\DI\Attributes\NotShared;
use Popcorn\Http\Contracts\Request;
use Popcorn\Http\Contracts\Router;
use RuntimeException;

#[NotShared, NoAutowiring]
final class HttpRuntime implements Runtime
{
    private(set) Popcorn $popcorn;

    private(set) Router $router;

    private(set) Request $request;

    /**
     * Set the popcorn instance for use in the runtime.
     *
     * @param \Popcorn\Core\Popcorn $popcorn
     *
     * @return static
     */
    public function setPopcorn(Popcorn $popcorn): static
    {
        $this->popcorn = $popcorn;

        return $this;
    }

    public function setRouter(Router $router): self
    {
        $this->router = $router;

        return $this;
    }


    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function boot(): void
    {
        // Nothing to boot.
    }

    /**
     * Run the runtime.
     *
     * @return void
     */
    public function run(): void
    {
        if (! isset($this->request)) {
            throw new RuntimeException('Request is not set.');
        }

        dd($this->request);
    }
}
