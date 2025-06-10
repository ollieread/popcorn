<?php
declare(strict_types=1);

namespace Popcorn\Http\Routing;

use Popcorn\Http\Contracts\Router;

final class RouterBuilder
{
    /**
     * @var list<string>
     */
    private array $routePaths = [];

    /**
     * @param list<string> $routePaths
     * @param bool         $overwrite
     *
     * @return static
     */
    public function discoverRoutesIn(array $routePaths, bool $overwrite = false): self
    {
        if ($overwrite) {
            $this->routePaths = $routePaths;
        } else {
            $this->routePaths = array_merge(
                $this->routePaths,
                $routePaths,
            );
        }

        return $this;
    }

    public function build(): Router
    {

    }
}
