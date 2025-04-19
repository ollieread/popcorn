<?php
declare(strict_types=1);

namespace App\Controllers;

use Popcorn\Http\Attributes\Route;
use Popcorn\Http\Contracts\Request;
use Popcorn\Http\RequestMethod;

final class ExampleController
{
    #[Route(RequestMethod::GET, '/example', 'example')]
    public function __invoke(Request $request): void
    {

    }
}
