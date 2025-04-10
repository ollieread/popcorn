<?php
declare(strict_types=1);

namespace Popcorn\Config;

final readonly class AppConfig
{
    public string $environment;

    public bool $debug;

    public function __construct(
        string $environment = 'local',
        bool   $debug = false,
    )
    {
        $this->environment = $environment;
        $this->debug       = $debug;
    }
}
