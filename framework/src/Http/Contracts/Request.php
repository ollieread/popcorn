<?php

namespace Popcorn\Http\Contracts;

use Popcorn\Http\RequestMethod;

interface Request
{
    /**
     * Get the request method.
     *
     * @return \Popcorn\Http\RequestMethod
     */
    public function method(): RequestMethod;
}
