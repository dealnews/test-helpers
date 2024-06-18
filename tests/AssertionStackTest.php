<?php

namespace DealNews\TestHelpers\Tests;

use \DealNews\TestHelpers\AssertionStack;

class AssertionStackTest extends \PHPUnit\Framework\TestCase {
    /**
     * Tests to make sure we're correctly failing on an assertion that the number of parameters passed to the
     * mock method is correct
     */
    public function testCountFailure() {
        $mock = new class extends \StdClass {
            use AssertionStack;

            public function test($var1, $var2, $var3) {
                $this->callAssertEqualFromStack(__FUNCTION__, func_get_args());
            }
        };

        $this->expectException('\\PHPUnit\\Framework\\ExpectationFailedException');
        $this->expectExceptionMessage('The number of expected parameters and passed-in parameters does not match for the method that is a mock of stdClass::test()');

        $mock->setTestCaseForMock($this);
        $mock->addAssertEqualStack('test', [
            'var1' => 'foo',
            'var2' => 'bar',
            'var3' => 'baz',
        ]);

        $mock->test('foo', 'bar', 'baz', 'hat');
    }

    /**
     * Tests to mak sure we're correctly failing on an assertion that one of the parameter values that was passed
     * to the mock method is not correct
     */
    public function testParameterValueFailure() {
        $mock = new class extends \StdClass {
            use AssertionStack;

            public function test($var1, $var2, $var3) {
                $this->callAssertEqualFromStack(__FUNCTION__, func_get_args());
            }
        };

        $this->expectException('\\PHPUnit\\Framework\\ExpectationFailedException');
        $this->expectExceptionMessage('var3 parameter does not have the expected value for the method that is a mock of stdClass::test()');

        $mock->setTestCaseForMock($this);
        $mock->addAssertEqualStack('test', [
            'var1' => 'foo',
            'var2' => 'bar',
            'var3' => 'baz',
        ]);

        $mock->test('foo', 'bar', 'hat');
    }

    public function testSuccess() {
        $mock = new class extends \StdClass {
            use AssertionStack;

            public function test($var1, $var2, $var3) {
                $this->callAssertEqualFromStack(__FUNCTION__, func_get_args());
            }
        };

        $mock->setTestCaseForMock($this);
        $mock->addAssertEqualStack('test', [
            'var1' => 'foo',
            'var2' => 'bar',
            'var3' => 'baz',
        ]);

        $mock->test('foo', 'bar', 'baz');
    }
}
