<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Popcorn\Container\Container;

class ContainerTest extends TestCase
{
    #[Test]
    public function container_exists(): void
    {
        $this->assertTrue(class_exists(Container::class));
    }
}
