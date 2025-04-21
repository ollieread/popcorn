<?php
declare(strict_types=1);

namespace Popcorn\Core;

use Popcorn\Core\Bootstrappers\EnvLoader;
use Popcorn\Core\Bootstrappers\LoadAndConfigureServiceContainer;
use Popcorn\Core\Bootstrappers\LoadConfigObjectsFromFiles;
use Popcorn\DI\Contracts\ServiceContainer;

final class PopcornBuilder
{
    /**
     * The service container loader.
     *
     * @var (callable(\Popcorn\Core\Popcorn): ?\Popcorn\DI\Contracts\ServiceContainer)|null
     */
    private $containerLoader;

    /**
     * The service container.
     *
     * @var \Popcorn\DI\Contracts\ServiceContainer|null
     */
    private ?ServiceContainer $container = null;

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
     * Service providers to construct the container.
     *
     * @var list<class-string<\Popcorn\DI\Contracts\ServiceProvider>>
     */
    private array $providers = [];

    private(set) ?string $cachePath = null;

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
     * @param callable(\Popcorn\Core\Popcorn): ?\Popcorn\DI\Contracts\ServiceContainer $loader
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

    /**
     * @param list<class-string<\Popcorn\DI\Contracts\ServiceProvider>> $providers
     * @param bool                                                      $overwrite
     *
     * @return $this
     */
    public function usingProviders(array $providers, bool $overwrite = false): self
    {
        if ($overwrite) {
            $this->providers = $providers;
        } else {
            $this->providers = array_merge($this->providers, $providers);
        }

        return $this;
    }

    /**
     * Set the cache directory.
     *
     * @param string $cacheDir
     *
     * @return self
     */
    public function useCacheIn(string $cacheDir): self
    {
        $this->cachePath = $cacheDir;

        return $this;
    }

    public function build(): Popcorn
    {
        // Make sure the env file path is set.
        $this->setEnvFilePath();

        // Make sure the config map is set.
        $this->setConfigMap();

        // Make sure the container loader is set.
        $this->setContainerLoader();

        return new Popcorn($this->bootstrappers, $this->cachePath);
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
        LoadConfigObjectsFromFiles::setConfigMap($this->configMap);
    }

    private function setContainerLoader(): void
    {
        if ($this->containerLoader === null) {
            return;
        }

        LoadAndConfigureServiceContainer::setLoader($this->containerLoader);
    }
}
