<?php

namespace SouthCN\PrivateApi\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ApiCache
{
    protected $rule;

    public function __construct(string $rule)
    {
        $this->rule = $rule;
    }

    public function get(string $key)
    {
        return Cache::get("api:$key");
    }

    public function smartCache(string $key, $response): void
    {
        if (Str::contains($this->rule, 'seconds')) {
            $seconds = str_replace(' seconds', '', $this->rule);
            $interval = now()->addSeconds($seconds);

            Cache::put("api:$key", $response, $interval);
        }
    }
}
