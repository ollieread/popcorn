<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Popcorn\Config\AppConfig;
use Popcorn\Config\EnvVars;
use Popcorn\Container\Container;
use Popcorn\Container\Contracts\Factory;
use Popcorn\Container\Factories\ManualFactory;

class ContainerTest extends TestCase
{
    #[Test]
    public function works_with_manual_dependencies(): void
    {
        $container = new Container(new ManualFactory([
            EnvVars::class   => static fn () => new EnvVars(['DEBUG' => true]),
            AppConfig::class => static function (Factory $f): AppConfig {
                $env = $f->get(EnvVars::class);
                /** @var \Popcorn\Config\AppConfig */
                return require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config/app.config.php';
            },
        ]));

        $this->assertTrue($container->has(EnvVars::class));
        $this->assertTrue($container->has(AppConfig::class));
        $this->assertTrue($container->get(EnvVars::class)->get('DEBUG'));
        $this->assertTrue($container->get(AppConfig::class)->debug);
    }
}
