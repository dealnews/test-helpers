<?php

namespace DealNews\TestHelpers\Tests\Classes;

/**
 * A simple class that can be mocked for testing purposes
 */
class StaticTestOneParam {
    public static function test(string $foo) : string {
        return 'statictestoneparams';
    }
}
