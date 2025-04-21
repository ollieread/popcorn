<?php
declare(strict_types=1);

namespace Popcorn\DI\Exceptions;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

final class ServiceNotFound extends RuntimeException implements NotFoundExceptionInterface
{
    public static function make(string $service): self
    {
        return new self(sprintf(
            'Service "%s" not found.',
            $service
        ));
    }
}
