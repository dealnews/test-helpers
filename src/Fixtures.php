<?php

namespace DealNews\TestHelpers;

/**
 * Helper functions for using fixtures
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @author      Jeremy Earle <jearle@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     \DealNews\TestHelpers
 */
trait Fixtures {

    /**
     * Directory where test are stored
     */
    public static $test_directory;

    /**
     * Directory where fixtures are stored
     */
    public static $fixture_directory;

    /**
     * Set test dir and fixture dir
     */
    public static function setUpBeforeClass(): void {
        if (!defined('TEST_DIR')) {
            define('TEST_DIR', null);
        }

        if (!defined('FIXTURE_DIR')) {
            define('FIXTURE_DIR', null);
        }

        self::$test_directory    = realpath(self::$test_directory ?? TEST_DIR ?? __DIR__ . '/../../../../tests');
        self::$fixture_directory = realpath(self::$fixture_directory ?? FIXTURE_DIR ?? self::$test_directory . '/fixtures');

        if (empty(self::$test_directory)) {
            throw new \RuntimeException('Unable to find test directory.');
        }
    }

    /**
     * Wrapper for assertSame that sorts array keys before comparing the
     * arrays. If the values are not arrays, there is no difference with
     * this method and assertSame
     *
     * @param      mixed   $expected  The expected value
     * @param      mixed   $actual    The actual value
     * @param      string  $message   The message
     */
    public function assertSameData(mixed $expected, mixed $actual, string $message = ''): void {
        if (is_array($expected) && is_array($actual)) {
            $expected = $this->sortKeysRecursive($expected);
            $actual   = $this->sortKeysRecursive($actual);
        }

        $this->assertSame($expected, $actual, $message);
    }

    /**
     * Returns the path to a fixture file
     *
     * @param      string  $fixture  The fixture name
     *
     * @return     string  The fixture filename.
     */
    public function getFixtureFile(string $fixture): string {

        // data providers are loaded before setUpBeforeClass is run
        // so make sure it has been called before using the variables
        if (self::$fixture_directory === null) {
            self::setUpBeforeClass();
        }

        $file = realpath(self::$fixture_directory . "/$fixture");
        $this->assertTrue(!empty($file) && file_exists($file), "Fixture $fixture does not exist");

        return $file;
    }

    /**
     * Returns the contents of a fixture file
     *
     * @param      string  $fixture  The fixture name
     *
     * @return     string  The fixture data.
     */
    public function getFixtureData(string $fixture): string {
        return file_get_contents($this->getFixtureFile($fixture));
    }

    /**
     * Returns fixture data JSON decoded
     *
     * @param      string  $fixture  The fixture name
     *
     * @return     array|object   The fixture data.
     */
    public function getFixtureJson(string $fixture, bool $as_array = true) {
        return json_decode($this->getFixtureData($fixture), $as_array);
    }

    /**
     * Returns fixture data as an array of JSON decoded lines
     *
     * @param      string  $fixture  The fixture name
     *
     * @return     array   The fixture data.
     */
    public function getFixtureJsonLines(string $fixture, bool $as_array = true): array {
        $data = [];

        $fp = fopen($this->getFixtureFile($fixture), 'r');

        while (!feof($fp)) {
            $line = trim(fgets($fp));
            if (!empty($line)) {
                $record = json_decode($line, $as_array);
                if (is_array($record) || is_object($record)) {
                    $data[] = $record;
                }
            }
        }

        return $data;
    }

    /**
     * Checks if a fixture string is a fixture file
     *
     * @param      string  $fixture  The fixture
     *
     * @return     bool    True if the specified fixture is fixture file, False otherwise.
     */
    protected function isFixtureFile(string $fixture): bool {
        if (!empty($fixture)) {
            $file = realpath(self::$fixture_directory . "/$fixture");
        }

        return !empty($file) && file_exists($file);
    }

    /**
     * Sorts arrays by key recursively
     *
     * @param      array  $arr    The arr
     *
     * @return     array
     */
    protected function sortKeysRecursive(array $arr): array {
        ksort($arr);
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $arr[$k] = $this->sortKeysRecursive($v);
            }
        }

        return $arr;
    }
}
