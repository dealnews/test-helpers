<?php

namespace DealNews\TestHelpers\Tests;

use \DealNews\TestHelpers\ReflectionHelper;

class ReflectionHelperTest extends \PHPUnit\Framework\TestCase {
    use ReflectionHelper;

    public function testExecuteNonPublicMethod() {
        $test = new class {
            protected function aProtectedMethod($args = null) {
                return $args;
            }

            private function aPrivateMethod($args = null) {
                return $args;
            }
        };

        // protected method, no arguments passed
        $result = $this->executeNonPublicMethod($test, 'aProtectedMethod');
        $this->assertEquals(null, $result, 'aProtectedMethod with no arguments passed did not return the expected value');

        // protected method, with a string argument passed
        $result = $this->executeNonPublicMethod($test, 'aProtectedMethod', ['hello world']);
        $this->assertEquals('hello world', $result, 'aProtectedMethod with a string argument passed did not return the expected value');

        // private method, no arguments passed
        $result = $this->executeNonPublicMethod($test, 'aPrivateMethod');
        $this->assertEquals(null, $result, 'aPrivateMethod with no arguments passed did not return the expected value');

        // private method, with a string argument passed
        $result = $this->executeNonPublicMethod($test, 'aPrivateMethod', ['hello world']);
        $this->assertEquals('hello world', $result, 'aPrivateMethod with a string argument passed did not return the expected value');
    }

    public function testGetNonPublicPropertyValue() {
        $test                                      = new class {
            protected string $a_protected_property = 'hello world, protected';

            private string $a_private_property = 'hello world, private';

            protected string $uninitialized;
        };

        $result = $this->getNonPublicPropertyValue($test, 'a_protected_property');
        $this->assertEquals('hello world, protected', $result, 'Did not get the expected value for a_protected_property');

        $result = $this->getNonPublicPropertyValue($test, 'a_private_property');
        $this->assertEquals('hello world, private', $result, 'Did not get the expected value for a_private_property');

        $result = $this->getNonPublicPropertyValue($test, 'uninitialized');
        $this->assertEquals(null, $result, 'Did not get the expected value for uninitialized');
    }

    public function testSetNonPublicPropertyValue() {
        $test                                      = new class {
            protected string $a_protected_property = 'hello world, protected';

            private string $a_private_property = 'hello world, private';

            protected string $uninitialized;

            public function getAProtectedPropertyValue() {
                return $this->a_protected_property;
            }

            public function getAPrivatePropertyValue() {
                return $this->a_private_property;
            }

            public function getUninitializedValue() {
                if (!isset($this->uninitialized)) {
                    // not initialized, yet
                    return null;
                } else {
                    return $this->uninitialized;
                }
            }
        };

        $this->setNonPublicPropertyValue($test, 'a_protected_property', 'different value, protected');
        $this->assertEquals('different value, protected', $test->getAProtectedPropertyValue(), 'Did not get the expected value for a_protected_property');

        $this->setNonPublicPropertyValue($test, 'a_private_property', 'different value, private');
        $this->assertEquals('different value, private', $test->getAPrivatePropertyValue(), 'Did not get the expected value for a_private_property');

        $this->setNonPublicPropertyValue($test, 'uninitialized', 'different value, uninitialized');
        $this->assertEquals('different value, uninitialized', $test->getUninitializedValue(), 'Did not get the expected value for uninitialized');
    }
}
