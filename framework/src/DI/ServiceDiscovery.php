<?php
declare(strict_types=1);

namespace Popcorn\DI;

class ServiceDiscovery
{
    /**
     * The namespace to directory mappings for discovery.
     *
     * @var array<string, list<string>>
     */
    private array $namespaceMappings;

    /**
     * Files that should be excluded from discovery.
     *
     * @var list<string>
     */
    private array $excludeFiles;

    public function __construct(
        array $namespaceMappings = [],
        array $excludedFiles = []
    )
    {
        $this->namespaceMappings = $namespaceMappings;
        $this->excludeFiles      = $excludedFiles;
    }
}
