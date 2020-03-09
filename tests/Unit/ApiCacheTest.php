<?php

namespace Tests\Unit;

use SouthCN\PrivateApi\Repositories\ApiCache;
use Tests\TestCase;

class ApiCacheTest extends TestCase
{
    public function testSmartCache()
    {
        $cache = new ApiCache('1 seconds');

        $cache->smartCache('aaa', 111);
        $this->assertEquals(111, $cache->get('aaa'));
    }
}
