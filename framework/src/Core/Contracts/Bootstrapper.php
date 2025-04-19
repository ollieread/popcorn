<?php

namespace Popcorn\Core\Contracts;

use Popcorn\Core\Popcorn;

interface Bootstrapper
{
    /**
     * Perform the bootstrapping.
     *
     * This method is called during the application boot phase.
     *
     * @param \Popcorn\Core\Popcorn $popcorn
     *
     * @return void
     */
    public function bootstrap(Popcorn $popcorn): void;
}
