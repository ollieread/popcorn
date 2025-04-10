<?php
declare(strict_types=1);

namespace Popcorn\Http;

final class HttpKernel
{
    private(set) Contracts\Request $request;

    public function setRequest(Contracts\Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function handle(Contracts\Request $request): void
    {
        $this->setRequest($request);

        echo '<pre>';
        var_dump($request);
        echo '</pre>';
        exit;
    }
}
