<?php
declare(strict_types=1);

namespace Popcorn\DI\Exceptions;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;
use Throwable;

final class ServiceContainerException extends RuntimeException implements ContainerExceptionInterface
{
    public static function unresolvable(string $service, ?Throwable $throwable = null): self
    {
        return new self(
            message : sprintf('The service "%s" cannot be resolved.', $service),
            previous: $throwable
        );
    }

    public static function notInstantiable(string $service): self
    {
        return new self(
            message: sprintf('The service "%s" cannot be instantiated.', $service)
        );
    }

    public static function unresolvableParameter(string $parameter, string $function, ?string $service = null, ?Throwable $throwable = null): self
    {
        return new self(
            message : sprintf('Unable to resolve parameter "%s" on "%s"', $parameter, $service ? ($service . '::' . $function) : $function),
            previous: $throwable
        );
    }

    public static function notScoped(string $service): self
    {
        return new self(
            message: sprintf('The service "%s" is not scoped.', $service)
        );
    }

    public static function uncallable(?Throwable $throwable = null): self
    {
        return new self(
            message : 'Unable to successfully call the provided callable.',
            previous: $throwable
        );
    }

    public static function uncallableMethod(string $method, string $class, ?Throwable $throwable = null): self
    {
        return new self(
            message : sprintf('Unable to successfully call "%s" on "%s".', $method, $class),
            previous: $throwable
        );
    }

    public static function instanceAlreadySet(string $service): self
    {
        return new self(
            message: sprintf('The service "%s" is already set.', $service)
        );
    }
}
