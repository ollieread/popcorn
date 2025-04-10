<?php
declare(strict_types=1);

namespace Popcorn\Http;

final class Request implements Contracts\Request
{
    /**
     * @var \Popcorn\Http\RequestMethod
     */
    private RequestMethod $method;

    private string $uri;

    /**
     * @var array<string|int, scalar|array<string|int, scalar>>
     */
    private array $query;

    /**
     * @var array<string, string|list<string>>
     */
    private array $headers;

    /**
     * @var array<string, scalar>
     */
    private array $cookies;

    /**
     * @param \Popcorn\Http\RequestMethod                         $method
     * @param string                                              $uri
     * @param array<string|int, scalar|array<string|int, scalar>> $query
     * @param array<string, string|list<string>>                  $headers
     * @param array<string, scalar>                               $cookies
     */
    public function __construct(
        RequestMethod $method,
        string        $uri,
        array         $query = [],
        array         $headers = [],
        array         $cookies = [],
    )
    {
        $this->method  = $method;
        $this->uri     = $uri;
        $this->cookies = $cookies;
        $this->headers = $headers;
        $this->query   = $query;
    }

    /**
     * The request method/verb.
     *
     * @return \Popcorn\Http\RequestMethod
     */
    public function method(): RequestMethod
    {
        return $this->method;
    }

    /**
     * The request URI.
     *
     * @return string
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * The request query variables.
     *
     * @return array<string|int, scalar|array<string|int, scalar>>
     */
    public function query(): array
    {
        return $this->query;
    }

    /**
     * The request headers.
     *
     * @return array<string, string|list<string>>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * The request cookies.
     *
     * @return array<string, scalar>
     */
    public function cookies(): array
    {
        return $this->cookies;
    }
}
