<?php
declare(strict_types=1);

namespace Popcorn\Http;

use Popcorn\DI\Attributes\NoAutowiring;
use Popcorn\DI\Attributes\NotShared;

#[NotShared, NoAutowiring]
class Request implements Contracts\Request
{
    private(set) readonly RequestMethod $method;

    public function __construct(
        RequestMethod $method,
    )
    {
        $this->method = $method;
    }

    /**
     * Get the request method.
     *
     * @return \Popcorn\Http\RequestMethod
     */
    public function method(): RequestMethod
    {
        return $this->method;
    }
}
