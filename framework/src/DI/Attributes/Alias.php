<?php
declare(strict_types=1);

namespace Popcorn\DI\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Alias
{
    /**
     * @var class-string
     */
    public string $alias;

    /**
     * @param class-string $alias
     */
    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }
}
