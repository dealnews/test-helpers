<?php

namespace DealNews\TestHelpers\Tests;

use \DealNews\TestHelpers\Fixtures;

class FixturesTest extends \PHPUnit\Framework\TestCase {

    protected static $instance;

    public static function setUpBeforeClass(): void {
        self::$instance = new class extends \PHPUnit\Framework\TestCase {
            use Fixtures;
            public function __construct() {
                // noop
            }
        };
    }

    public function testSetup() {
        self::$instance::setUpBeforeClass();

        $this->assertEquals(
            __DIR__,
            self::$instance::$test_directory
        );

        $this->assertEquals(
            __DIR__ . '/fixtures',
            self::$instance::$fixture_directory
        );
    }

    public function testGetFixtureFile() {
        self::$instance::setUpBeforeClass();
        $result = self::$instance->getFixtureFile('foo.json');
        $this->assertEquals(
            realpath(__DIR__ . '/fixtures/foo.json'),
            $result
        );
    }

    public function testGetFixtureData() {
        self::$instance::setUpBeforeClass();
        $result = self::$instance->getFixtureData('foo.json');
        $this->assertEquals(
            '{"foo":true}',
            $result
        );
    }

    public function testGetFixtureJson() {
        self::$instance::setUpBeforeClass();
        $result = self::$instance->getFixtureJson('foo.json');
        $this->assertEquals(
            ['foo' => true],
            $result
        );
    }

    public function testGetFixtureJsonLines() {
        self::$instance::setUpBeforeClass();
        $result = self::$instance->getFixtureJsonLines('foo.jsonl');
        $this->assertEquals(
            [
                ['foo' => true],
                ['foo' => false],
            ],
            $result
        );
    }
}
