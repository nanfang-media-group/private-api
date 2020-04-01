<?php

namespace SouthCN\PrivateApi\Repositories;

use AbelHalo\ApiProxy\ApiProxy;
use Illuminate\Support\Arr;

class Hook
{
    protected $hooks;

    public function __construct(array $hooks)
    {
        $this->hooks = $hooks;
    }

    public function run(string $hook, ApiProxy $proxy, string &$url, array &$params): void
    {
        if ($class = Arr::get($this->hooks, $hook)) {
            app($class)($proxy, $url, $params);
        }
    }
}
