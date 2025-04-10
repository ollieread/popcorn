<?php
declare(strict_types=1);

namespace Popcorn\Http;

/**
 * Request Methods
 *
 * An enumeration of HTTP request methods.
 *
 * @phpstan-pure
 */
enum RequestMethod: string
{
    case GET = 'GET';

    case HEAD = 'HEAD';

    case POST = 'POST';

    case PUT = 'PUT';

    case DELETE = 'DELETE';

    case CONNECT = 'CONNECT';

    case OPTIONS = 'OPTIONS';

    case TRACE = 'TRACE';

    case PATCH = 'PATCH';

    /**
     * Get the name of the request method
     *
     * @return string
     */
    public function name(): string
    {
        return $this->value;
    }

    /**
     * Get whether the request method is safe
     *
     * @return bool
     */
    public function isSafe(): bool
    {
        return match ($this) {
            self::GET, self::HEAD, self::OPTIONS, self::TRACE => true,
            default                                           => false,
        };
    }

    /**
     * Get whether the request method is idempotent
     *
     * @return bool
     */
    public function isIdempotent(): bool
    {
        return match ($this) {
            self::POST, self::PATCH, self::CONNECT => false,
            default                                => true
        };
    }

    /**
     * Get whether the request method is cacheable
     *
     * @return bool
     */
    public function isCacheable(): bool
    {
        return match ($this) {
            self::GET, self::HEAD => true,
            default               => false,
        };
    }

    /**
     * Get whether the request method is conditionally cacheable
     *
     * @return bool
     */
    public function isConditionallyCacheable(): bool
    {
        return match ($this) {
            self::POST, self::PATCH => true,
            default                 => false,
        };
    }

    /**
     * Get whether the request method has a request body
     *
     * @return bool
     */
    public function hasRequestBody(): bool
    {
        return match ($this) {
            self::POST, self::PUT, self::PATCH, self::DELETE => true,
            default                                          => false,
        };
    }

    /**
     * Get whether the request method has a response body
     *
     * @return bool
     */
    public function hasResponseBody(): bool
    {
        return match ($this) {
            self::HEAD, self::CONNECT => false,
            default                   => true
        };
    }
}
