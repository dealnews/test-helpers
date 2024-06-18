<?php

namespace DealNews\TestHelpers\Tests;

use \DealNews\TestHelpers\Guzzle;

class GuzzleTest extends \PHPUnit\Framework\TestCase {

    public function testMakeGuzzleMock() {
        $tc = new class extends \PHPUnit\Framework\TestCase {
            use Guzzle;
            public function __construct() {
                // noop
            }
        };

        $tc::setUpBeforeClass();

        $container = [];
        $client    = $tc->makeGuzzleMock(
            [
                200,
                404,
                200,
            ],
            [
                'foo.json',
                ['bar' => 2],
                null,
            ],
            $container
        );

        $this->assertTrue($client instanceof \GuzzleHttp\Client);
    }
}
