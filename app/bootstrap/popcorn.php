<?php
declare(strict_types=1);

use Popcorn\Core\Config\AppConfig;
use Popcorn\Core\FrameworkHelper;
use Popcorn\Core\Popcorn;

return Popcorn::builder()
              ->useDefaultBootstrappers()
              ->loadConfigFrom([
                  AppConfig::class => dirname(__DIR__) . '/config/app.php',
              ])
              ->loadContainerUsing(FrameworkHelper::containerLoader(
                  __DIR__ . '/cache/container-compiled.php',
                  __DIR__ . '/container.php'
              ));
