<?php

namespace Popcorn\Core\Contracts;

use Popcorn\Core\Popcorn;
use Popcorn\DI\Contracts\ServiceContainer;

interface Runtime
{
    /**
     * Set the popcorn instance for use in the runtime.
     *
     * @param \Popcorn\Core\Popcorn $popcorn
     *
     * @return static
     */
    public function setPopcorn(Popcorn $popcorn): static;

    /**
     * Boot the runtime.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Run the runtime.
     *
     * @return void
     */
    public function run(): void;
}
