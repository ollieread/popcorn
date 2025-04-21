<?php
declare(strict_types=1);

use Popcorn\Core\FrameworkHelper;

// Load the Composer autoloader.
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Load the Popcorn application builder.
 *
 * @var \Popcorn\Core\Popcorn $popcorn
 */
$popcorn = require dirname(__DIR__) . '/bootstrap/popcorn.php';

// Load the runtime and set it in the Popcorn instance.
$popcorn->setRuntime(FrameworkHelper::loadRuntime(
    dirname(__DIR__) . '/bootstrap/runtimes/http.php',
    $popcorn->container
));

// Then run the application.
$popcorn->run();
