<?php
declare(strict_types=1);

use Popcorn\Http\HttpRuntime;
use Popcorn\Http\RequestBuilder;

return new HttpRuntime()
    ->setRequest(
        new RequestBuilder()
            ->useSuperGlobals()
            ->build()
    )
    ->boot();
