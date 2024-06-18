<?php

namespace DealNews\TestHelpers\Tests;

use \DealNews\TestHelpers\Methods;
use \DealNews\TestHelpers\Tests\Classes\StaticTestNoParams;
use \DealNews\TestHelpers\Tests\Classes\StaticTestOneParam;
use \DealNews\TestHelpers\Tests\Classes\TestInterface;
use \DealNews\TestHelpers\Tests\Classes\TestNoParams;
use \DealNews\TestHelpers\Tests\Classes\TestOneParam;

class MethodsTest extends \PHPUnit\Framework\TestCase {
    public function testCheckMethodExists() {
        $child = new class extends TestNoParams {
            use Methods;

            public function test(): string {
                return $this->_getNextResponse(__FUNCTION__, 'default');
            }
        };

        $child->_addMultipleResponses('test', ['foo']);

        $this->assertTrue(true);

        $bare_mock = new class implements TestInterface {
            use Methods;

            public function test(): string {
                return $this->_getNextResponse(__FUNCTION__, 'default');
            }
        };

        $bare_mock->_addMultipleResponses('test', ['foo']);

        $this->assertTrue(true);
    }

    public function testCheckMethodExistsExceptionChild() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Method test2 not found for DealNews\\TestHelpers\\Tests\\Classes\\TestNoParams when attempting to add a mock response with the Mock\\Methods trait.');

        $child = new class extends TestNoParams {
            use Methods;

            public function test(): string {
                return $this->_getNextResponse(__FUNCTION__, 'default');
            }
        };

        $child->_addMultipleResponses('test2', ['foo']);
    }

    public function testCheckMethodExistsExceptionBare() {
        $this->expectException(\InvalidArgumentException::class);
        // expectExceptionMessage checks the string is contained, not an exact match
        $this->expectExceptionMessage('Method test2 not found');

        $bare_mock = new class implements TestInterface {
            use Methods;

            public function test(): string {
                return $this->_getNextResponse(__FUNCTION__, 'default');
            }
        };

        $bare_mock->_addMultipleResponses('test2', ['foo']);
    }

    public function testCheckMethodExistsExceptionBareNoInterface() {
        $this->expectException(\LogicException::class);
        // expectExceptionMessage checks the string is contained, not an exact match
        $this->expectExceptionMessage('class@anonymous');

        $bare_mock = new class {
            use Methods;

            public function test(): string {
                return $this->_getNextResponse(__FUNCTION__, 'default');
            }
        };

        $bare_mock->_addMultipleResponses('test', ['foo']);
    }

    public function testGetNextResponse() {
        $object = new class extends TestNoParams {
            use Methods;

            public function test(): string {
                return $this->_getNextResponse(__FUNCTION__, 'default');
            }
        };

        $object->_addMultipleResponses(
            'test',
            [
                'true',
                'false',
                'false',
                'true',
                'true',
            ]
        );

        $this->assertEquals('true', $object->test());
        $this->assertEquals('false', $object->test());
        $this->assertEquals('false', $object->test());
        $this->assertEquals('true', $object->test());
        $this->assertEquals('true', $object->test());

        $this->assertEquals(5, $object->method_counts['test']);

        // test default value
        $this->assertEquals('default', $object->test());
        $this->assertEquals(6, $object->method_counts['test']);

        // test _addMultipleMethodResponses
        $object->_addMultipleMethodResponses([
            'test' => [
                'some',
                'different',
                'results',
                'than',
                'before',
            ],
        ]);

        $this->assertEquals('some', $object->test());
        $this->assertEquals('different', $object->test());
        $this->assertEquals('results', $object->test());
        $this->assertEquals('than', $object->test());
        $this->assertEquals('before', $object->test());

        $this->assertEquals(5, $object->method_counts['test']);
    }

    public function testGetResponseWithParams() {
        $object = new class extends TestOneParam {
            use Methods;

            public function test(string $foo): string {
                return $this->_getResponseWithParams(__FUNCTION__, func_get_args(), 'default');
            }
        };

        $object->_addReturnValueWithParams(
            'test',
            ['true_test'],
            'true'
        );

        $object->_addReturnValueWithParams(
            'test',
            ['false_test'],
            'false'
        );

        $this->assertEquals('false', $object->test('false_test'));
        $this->assertEquals('true', $object->test('true_test'));
        $this->assertEquals('default', $object->test('default_test'));

        $this->assertEquals(3, $object->method_counts['test']);
    }

    public function testGetNextStaticResponse() {
        $object = new class extends StaticTestNoParams {
            use Methods;

            public static function test(): string {
                return self::_getNextStaticResponse(__FUNCTION__, 'default');
            }
        };

        $object::_addMultipleStaticResponses(
            'test',
            [
                'true',
                'false',
                'false',
                'true',
                'true',
                'false', // sixth one should be used due to reset below
            ]
        );

        $this->assertEquals('true', $object::test());
        $this->assertEquals('false', $object::test());
        $this->assertEquals('false', $object::test());
        $this->assertEquals('true', $object::test());
        $this->assertEquals('true', $object::test());

        $this->assertEquals(5, $object::$static_method_counts['test']);

        // test reset
        $object::_resetStaticResponses();
        $this->assertEquals('default', $object::test());
        $this->assertEquals(1, $object::$static_method_counts['test']);

        // test default value
        $this->assertEquals('default', $object::test());
        $this->assertEquals(2, $object::$static_method_counts['test']);

        // test _addMultipleStaticMethodResponses
        $object::_addMultipleStaticMethodResponses([
            'test' => [
                'some',
                'different',
                'results',
                'than',
                'before',
            ],
        ]);

        $this->assertEquals('some', $object::test());
        $this->assertEquals('different', $object::test());
        $this->assertEquals('results', $object::test());
        $this->assertEquals('than', $object::test());
        $this->assertEquals('before', $object::test());

        $this->assertEquals(5, $object::$static_method_counts['test']);
    }

    public function testGetStaticResponseWithParams() {
        $object = new class extends StaticTestOneParam {
            use Methods;

            public static function test(string $foo): string {
                return self::_getStaticResponseWithParams(__FUNCTION__, func_get_args(), 'default');
            }
        };

        $object::_addStaticReturnValueWithParams(
            'test',
            ['true_test'],
            'true'
        );

        $object::_addStaticReturnValueWithParams(
            'test',
            ['false_test'],
            'false'
        );

        $object::_addStaticReturnValueWithParams(
            'test',
            ['reset_test'],
            'reset'
        );

        $this->assertEquals('false', $object::test('false_test'));
        $this->assertEquals('true', $object::test('true_test'));
        $this->assertEquals(2, $object::$static_method_counts['test']);

        //test reset
        $object::_resetStaticResponses();
        $this->assertEquals('default', $object::test('reset_test'));

        //test default
        $this->assertEquals('default', $object::test('default_test'));

        $this->assertEquals(2, $object::$static_method_counts['test']);
    }
}
