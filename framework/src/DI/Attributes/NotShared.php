<?php
declare(strict_types=1);

namespace Popcorn\DI\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class NotShared
{

}
