<?php

namespace Popcorn\Http\Contracts;

use Popcorn\Http\RequestMethod;

/**
 *
 */
interface Request
{
    /**
     * The request method/verb.
     *
     * @return \Popcorn\Http\RequestMethod
     */
    public function method(): RequestMethod;

    /**
     * The request URI.
     *
     * @return string
     */
    public function uri(): string;

    /**
     * The request query variables.
     *
     * @return array<string|int, scalar|array<string|int, scalar>>
     */
    public function query(): array;

    /**
     * The request headers.
     *
     * @return array<string, string|list<string>>
     */
    public function headers(): array;

    /**
     * The request cookies.
     *
     * @return array<string, scalar>
     */
    public function cookies(): array;
}
