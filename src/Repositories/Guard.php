<?php

namespace SouthCN\PrivateApi\Repositories;

class Guard
{
    protected $logic;

    public function __construct(?\Closure $logic = null)
    {
        $this->logic = $logic;
    }

    public function run(string $app, string $api): void
    {
        if (!is_callable($this->logic)) {
            return;
        }

        $flag = ($this->logic)($app, $api);

        if (!$flag) {
            abort(403, '无此API授权');
        }
    }
}
