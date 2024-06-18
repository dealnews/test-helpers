<?php

namespace DealNews\TestHelpers\Tests\Classes;

/**
 * A simple class that can be mocked for testing purposes
 */
class TestOneParam {
    public function test(string $foo) : string {
        return 'testoneparams';
    }
}
