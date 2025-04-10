<?php
declare(strict_types=1);

namespace Popcorn\Http;

use Closure;
use RuntimeException;

final class RequestFactory
{
    public static function make(): self
    {
        return new self();
    }

    private bool $useGlobals = false;

    private RequestMethod $method;

    /**
     * @var \Closure(string): array<string|int, scalar|array<string|int, scalar>>
     */
    private Closure $queryVarParser;

    public function fromGlobals(): self
    {
        $this->useGlobals = true;

        return $this;
    }

    public function method(RequestMethod $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param \Closure(string): array<string|int, scalar|array<string|int, scalar>> $callback
     *
     * @return self
     */
    public function parseQueryStringUsing(Closure $callback): self
    {
        $this->queryVarParser = $callback;

        return $this;
    }

    public function get(): Contracts\Request
    {
        return new Request(
            method : $this->getMethod(),
            uri    : $this->getUri(),
            query  : $this->getQueryVars(),
            headers: $this->getHeaders(),
            cookies: $this->getCookies(),
        );
    }

    private function getMethod(): RequestMethod
    {
        /** @var array{REQUEST_METHOD: string} $_SERVER */

        return $this->method ??
               (
               $this->useGlobals
                   ? RequestMethod::from(strtoupper($_SERVER['REQUEST_METHOD']))
                   : throw new RuntimeException('Request method isn\'t set')
               );
    }

    private function getUri(): string
    {
        /** @var array{REQUEST_URI?: string} $_SERVER */

        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    /**
     * @return array<string|int, scalar|array<string|int, scalar>>
     */
    private function getQueryVars(): array
    {
        /** @var array{QUERY_STRING?: string} $_SERVER */

        if (isset($this->queryVarParser)) {
            return ($this->queryVarParser)($_SERVER['QUERY_STRING'] ?? '');
        }

        if ($this->useGlobals) {
            /** @var array<string|int, scalar|array<string|int, scalar>> $_GET */
            return $_GET;
        }

        return [];
    }

    /**
     * @return array<string, string|list<string>>
     */
    private function getHeaders(): array
    {
        $headers = [];

        if ($this->useGlobals) {
            $rawHeaders = array_filter($_SERVER, static function ($key) {
                return str_starts_with($key, 'HTTP_') || str_starts_with($key, 'CONTENT_');
            }, ARRAY_FILTER_USE_KEY);

            /**
             * @var string $header
             * @var string $value
             */
            foreach ($rawHeaders as $header => $value) {
                $headers[ucwords(strtolower(str_replace(['HTTP_', '_'], ['', '-'], $header)), '-')] = $value;
            }
        }

        return $headers;
    }

    /**
     * @return array<string, scalar>
     */
    private function getCookies(): array
    {
        $cookies = [];

        if ($this->useGlobals) {
            /** @var array<string, scalar> $_COOKIE */
            $cookies = $_COOKIE;
        }

        return $cookies;
    }
}
