<?php

use Popcorn\Http\HttpKernel;
use Popcorn\Http\RequestFactory;

require __DIR__ . '/../vendor/autoload.php';

/** @var \Popcorn\Container\Contracts\Container $container */
$container = require __DIR__ . '/../bootstrap/app.php';

// Build the request
$request = RequestFactory::make()
                         ->fromGlobals()
                         ->get();

// Handle it
$container->get(HttpKernel::class)
          ->handle(RequestFactory::make()->fromGlobals()->get());
