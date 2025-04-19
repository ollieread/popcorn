<?php
declare(strict_types=1);

namespace Popcorn\Core\Config;

final readonly class AppConfig
{
    public string $environment;

    public bool $debug;

    public string $basePath;

    public string $vendorPath;

    public function __construct(
        string  $environment,
        bool    $debug,
        string  $basePath,
        ?string $vendorPath = null
    )
    {
        $this->environment = $environment;
        $this->debug       = $debug;
        $this->basePath    = $basePath;
        $this->vendorPath  = $vendorPath ?? $basePath . '/vendor';
    }
}
