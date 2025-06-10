<?php
declare(strict_types=1);

use Popcorn\Http\HttpRuntime;
use Popcorn\Http\RequestBuilder;
use Popcorn\Http\Routing\RouterBuilder;

return new HttpRuntime()
    ->setRouter(
        new RouterBuilder()
            ->discoverRoutesIn([
                dirname(__DIR__, 2) . '/app/Controllers',
            ])
            ->build()
    )->setRequest(
        new RequestBuilder()
            ->useSuperGlobals()
            ->build()
    );
