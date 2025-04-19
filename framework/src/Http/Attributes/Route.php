<?php
declare(strict_types=1);

namespace Popcorn\Http\Attributes;

use Attribute;
use Popcorn\Http\RequestMethod;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final readonly class Route
{
    public RequestMethod $method;

    public string $path;

    public string $name;

    public function __construct(
        RequestMethod $method,
        string        $path,
        string        $name
    )
    {
        $this->method = $method;
        $this->path   = '/' . trim($path, '/');
        $this->name   = $name;
    }
}
