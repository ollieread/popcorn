<?php
declare(strict_types=1);

namespace Popcorn\Core;

use Popcorn\Core\Bootstrappers\EnvLoader;
use Popcorn\Core\Bootstrappers\LoadConfigObjectsFromFiles;
use Popcorn\Core\Bootstrappers\PopulateEnvVarsFromEnvPhp;
use Popcorn\Core\Bootstrappers\RegisterCurrentContextStack;
use Popcorn\Core\Contracts\Runtime;
use Popcorn\DI\Contracts\ServiceContainer;
use RuntimeException;

final class PopcornBuilder
{
    /**
     * The service container loader.
     *
     * @var (callable(): \Popcorn\DI\Contracts\ServiceContainer)|null
     */
    private $containerLoader;

    /**
     * The service container.
     *
     * @var \Popcorn\DI\Contracts\ServiceContainer|null
     */
    private ?ServiceContainer $container = null;

    /**
     * The runtime loader.
     *
     * @var callable|null
     */
    private $runtimeLoader;

    /**
     * The runtime instance.
     *
     * @var \Popcorn\Core\Contracts\Runtime|null
     */
    private ?Runtime $runtime = null;

    /**
     * The bootstrappers to configure the application.
     *
     * @var list<class-string<\Popcorn\Core\Contracts\Bootstrapper>>
     */
    private array $bootstrappers = [];

    /**
     * The map of config classes to files.
     *
     * @var array<class-string, string>
     */
    private array $configMap = [];

    /**
     * The path to the env file.
     *
     * @var string
     */
    private string $envFilePath;

    /**
     * Using the given service container instance.
     *
     * @param \Popcorn\DI\Contracts\ServiceContainer $container
     *
     * @return static
     */
    public function withContainer(ServiceContainer $container): self
    {
        $this->containerLoader = null;
        $this->container       = $container;

        return $this;
    }

    /**
     * Use the given loader to resolve the service container.
     *
     * @param callable(): \Popcorn\DI\Contracts\ServiceContainer $loader
     *
     * @return self
     */
    public function loadContainerUsing(callable $loader): self
    {
        $this->container       = null;
        $this->containerLoader = $loader;

        return $this;
    }

    /**
     * Use the given runtime instance.
     *
     * @param \Popcorn\Core\Contracts\Runtime $runtime
     *
     * @return self
     */
    public function withRuntime(Runtime $runtime): self
    {
        $this->runtimeLoader = null;
        $this->runtime       = $runtime;

        return $this;
    }

    /**
     * Use the given loader to resolve the runtime.
     *
     * @param callable $loader
     *
     * @return self
     */
    public function loadRuntimeUsing(callable $loader): self
    {
        $this->runtime       = null;
        $this->runtimeLoader = $loader;

        return $this;
    }

    /**
     * Using the provided bootstrappers when bootstrapping the application.
     *
     * @param list<class-string<\Popcorn\Core\Contracts\Bootstrapper>> $bootstrappers
     *
     * @return self
     */
    public function usingBootstrappers(array $bootstrappers): self
    {
        $this->bootstrappers = $bootstrappers;

        return $this;
    }

    /**
     * Use the default set of bootstrappers.
     *
     * @return self
     */
    public function useDefaultBootstrappers(): self
    {
        return $this->usingBootstrappers([
            PopulateEnvVarsFromEnvPhp::class,
            LoadConfigObjectsFromFiles::class,
            RegisterCurrentContextStack::class,
        ]);
    }

    /**
     * Load config from the provided map of config class to file.
     *
     * @param array<class-string, string> $map
     *
     * @return self
     */
    public function loadConfigFrom(array $map): self
    {
        $this->configMap = $map;

        return $this;
    }

    /**
     * @param string $filePath
     *
     * @return self
     */
    public function loadEnvFrom(string $filePath): self
    {
        $this->envFilePath = $filePath;

        return $this;
    }

    public function build(): Popcorn
    {
        // Make sure the env file path is set.
        $this->setEnvFilePath();

        // Make sure the config map is set.
        $this->setConfigMap();

        // Get the container.
        $container = $this->getContainer();

        return new Popcorn(
            $container,
            $this->getRuntime($container),
            $this->bootstrappers,
        );
    }

    private function setEnvFilePath(): void
    {
        // If there's no path given, we'll just return.
        if (! isset($this->envFilePath)) {
            return;
        }

        // Set the file path on the base class.
        EnvLoader::setFilePath($this->envFilePath);
    }

    private function setConfigMap(): void
    {
        // If there's no config map given, we'll just return.
        if (! isset($this->configMap)) {
            return;
        }

        // Set the config map on the base class.
        LoadConfigObjectsFromFiles::setConfigMap($this->configMap);;
    }

    private function getContainer(): ServiceContainer
    {
        if ($this->containerLoader !== null) {
            $this->container = ($this->containerLoader)();
        }

        if ($this->container === null) {
            throw new RuntimeException('No container has been set.');
        }

        return $this->container;
    }

    private function getRuntime(ServiceContainer $container): Runtime
    {
        if ($this->runtimeLoader !== null) {
            $runtime = ($this->runtimeLoader)($container);
            /** @var \Popcorn\Core\Contracts\Runtime $runtime */
            $this->runtime = $runtime;
        }

        if ($this->runtime === null) {
            throw new RuntimeException('No runtime has been set.');
        }

        return $this->runtime;
    }
}
