<?php
declare(strict_types=1);

namespace Popcorn\Http;

use Popcorn\Http\Contracts\RequestContextRegistry;
use RuntimeException;

final class RequestBuilder
{
    private bool $useSuperGlobals = false;

    private RequestMethod $method;

    /**
     * @var callable(\Popcorn\Http\RequestContextStack): void
     */
    private $contextCallback;

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

    /**
     * @param callable(\Popcorn\Http\RequestContextStack): void $callback
     *
     * @return self
     */
    public function withContext(callable $callback): self
    {
        $this->contextCallback = $callback;

        return $this;
    }

    public function build(): Contracts\Request
    {
        return new Request(
            method : $this->getMethod(),
            context: $this->getRequestContext()
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

    private function getRequestContext(): RequestContextRegistry
    {
        $context = new RequestContextStack();

        if ($this->contextCallback !== null) {
            ($this->contextCallback)($context);
        }

        return $context;
    }
}
