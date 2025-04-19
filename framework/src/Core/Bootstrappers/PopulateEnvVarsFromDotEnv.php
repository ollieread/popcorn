<?php
declare(strict_types=1);

namespace Popcorn\Core\Bootstrappers;

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;

/**
 * Populate environment variables from .env
 * ========================================
 *
 * This class is responsible for loading environment variables from a PHP file
 * named <code>.env</code> using <code>vlucas/phpdotenv</code>.
 *
 * @package Bootstrappers
 */
final class PopulateEnvVarsFromDotEnv extends EnvLoader
{
    /**
     * Load the variables.
     *
     * @return array<string, mixed>
     */
    protected function loadVariables(): array
    {
        $dotenv = Dotenv::create(
            $this->getEnvRepository(),
            $this->getEnvPaths()
        );

        return  $dotenv->load();
    }

    private function getEnvRepository(): RepositoryInterface
    {
        return RepositoryBuilder::createWithNoAdapters()
                                ->addAdapter(EnvConstAdapter::class)
                                ->addAdapter(PutenvAdapter::class)
                                ->immutable()
                                ->make();
    }

    private function getEnvPaths(): string
    {
        return dirname(self::getFilePath(getcwd() . '/'));
    }
}
