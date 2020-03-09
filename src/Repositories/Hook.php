<?php

namespace SouthCN\PrivateApi\Repositories;

use AbelHalo\ApiProxy\ApiProxy;

class Hook
{
    protected $hooks;

    public function __construct(array $hooks)
    {
        $this->hooks = $hooks;
    }

    public function run(string $hook, ApiProxy $proxy, string &$url, array &$params): void
    {
        if ($class = array_get($this->hooks, $hook)) {
            app($class)($proxy, $url, $params);
        }
    }
}
