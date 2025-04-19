<?php
declare(strict_types=1);

use Popcorn\Core\FrameworkHelper;

// Load the Composer autoloader.
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Load the Popcorn application builder.
 *
 * @var \Popcorn\Core\PopcornBuilder $popcorn
 */
$popcorn = require dirname(__DIR__) . '/bootstrap/popcorn.php';

// Set the runtime, build the application, and run it.
$popcorn->loadRuntimeUsing(FrameworkHelper::runtimeLoader(dirname(__DIR__) . '/bootstrap/runtimes/http.php'))
        ->build()
        ->run();
