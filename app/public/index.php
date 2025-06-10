<?php
declare(strict_types=1);

use Popcorn\Core\FrameworkHelper;

// Load the Composer autoloader.
require dirname(__DIR__) . '/vendor/autoload.php';

// Load the early error handling.
// Might remove this later, but it's here for now.
require dirname(__DIR__) . '/bootstrap/errors.php';

/**
 * Load the Popcorn application builder.
 *
 * @var \Popcorn\Core\Popcorn $popcorn
 */
$popcorn = require dirname(__DIR__) . '/bootstrap/app.php';

// Load the runtime and set it in the Popcorn instance.
$popcorn->setRuntime(FrameworkHelper::loadRuntime(dirname(__DIR__) . '/bootstrap/runtimes/http.php'));

// Next, we make sure the application has booted.
$popcorn->boot();

// And then run the it.
$popcorn->run();
