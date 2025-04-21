<?php
declare(strict_types=1);

namespace Popcorn\Http;

use RuntimeException;

final class RequestBuilder
{
    private bool $useSuperGlobals = false;

    private RequestMethod $method;

    public function useSuperGlobals(): self
    {
        $this->useSuperGlobals = true;

        return $this;
    }

    public function doNotUseSuperGlobals(): self
    {
        $this->useSuperGlobals = false;

        return $this;
    }

    public function forMethod(RequestMethod $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function build(): Contracts\Request
    {
        return new Request(
            method: $this->getMethod()
        );
    }

    private function getMethod(): RequestMethod
    {
        if (isset($this->method)) {
            return $this->method;
        }

        if ($this->useSuperGlobals) {
            /** @var array{REQUEST_METHOD?:string} $_SERVER */
            return RequestMethod::from($_SERVER['REQUEST_METHOD'] ?? 'GET');
        }

        throw new RuntimeException('Request method is not set.');
    }
}
