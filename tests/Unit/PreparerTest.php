<?php

namespace Tests\Unit;

use SouthCN\PrivateApi\Repositories\Preparer;
use Tests\TestCase;

class PreparerTest extends TestCase
{
    /**
     * @var Preparer
     */
    protected $preparer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->preparer = new Preparer([
            'casts' => [
                'time_from' => 'timestamp -> datetime',
                'time_to' => 'timestamp -> datetime',
            ],
            'defaults' => [
                'offset' => 0,
                'limit' => 10,
            ],
        ]);
    }

    public function testCast()
    {
        $params = [
            'time_from' => 1548316904,
            'time_to' => 1548316904,
        ];

        $this->assertArraySubset([
            'time_from' => '2019-01-24 08:01:44',
            'time_to' => '2019-01-24 08:01:44',
        ], $this->preparer->cast($params), true);
    }

    public function testSetDefaults()
    {
        $params = ['abc' => 123];

        $this->assertArraySubset([
            'abc' => 123,
            'offset' => 0,
            'limit' => 10,
        ], $this->preparer->setDefaults($params), true);
    }
}
