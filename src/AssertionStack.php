<?php

namespace DealNews\TestHelpers;

/**
 * Trait used in mock objects. Allows one to set testcase assertions to be called inside
 * mocked methods as a way of checking parameters that may have been passed to the mocked
 * method.
 *
 * @author      Jeremy Earle <jearle@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     \DealNews\TestHelpers
 */
trait AssertionStack {

    /**
     * @var \PHPUnit\Framework\TestCase
     */
    protected static $testcase;

    /**
     * Stack of assertEqual calls for a method to check the method's parameters that were passed in
     *
     * @var        array
     */
    protected static $assert_equal_stacks = [];

    /**
     * Sets the currently used testcase object so that this mock class can reference and use
     * the assertEquals method
     *
     * @param \PHPUnit\Framework\TestCase $testCase
     */
    public function setTestCaseForMock(\PHPUnit\Framework\TestCase $testCase) : void {
        self::$testcase = $testCase;
    }

    /**
     * Resets the assert equal stack
     */
    public function resetAssertEqualStack() {
        self::$assert_equal_stacks = [];
    }

    /**
     * Adds multiple return values for a method
     *
     * @param      string  $func                    The method name
     * @param      array   $expected_param_values   A list of arrays. Each array contains a list of param values that are expected
     */
    public function setAssertEqualStack(string $func, array $expected_param_values) : void {
        foreach ($expected_param_values as $expected_param_value_array) {
            $this->addAssertEqualStack($func, $expected_param_value_array);
        }
    }

    /**
     * Adds a single set of expected parameters to be checked with assertEqual
     *
     * @param      string  $func                    The function
     * @param      array   $expected_param_values   An array of expected param values
     */
    public function addAssertEqualStack(string $func, array $expected_param_values) : void {
        self::$assert_equal_stacks[$func][] = $expected_param_values;
    }

    /**
     * Takes one set of expected param values off of the top of the stack for a particular func/method
     * and calls assertEqual for each parameter
     *
     * @param      string  $func     The function
     * @param      array   $params   The parameter values that were passed when the method was called.
     *
     */
    protected function callAssertEqualFromStack(string $func, array $params) : void {
        $classname = get_parent_class($this);

        if (empty(self::$testcase)) {
            trigger_error('The testcase object was never set. See: setTestCaseForMock() method that should exist in the mock class of ' . $classname, E_USER_ERROR);
        }

        if (!empty(self::$assert_equal_stacks[$func])) {
            $expected_values = array_shift(self::$assert_equal_stacks[$func]);

            self::$testcase->assertEquals(count($expected_values), count($params), 'The number of expected parameters and passed-in parameters does not match for the method that is a mock of ' . $classname . '::' . $func . '()');

            $reflect           = new \ReflectionMethod($this, $func);
            $param_definitions = $reflect->getParameters();

            foreach ($params as $parameter_number => $parameter_value) {
                $parameter_name = '[Unknown parameter name]';
                if (!empty($param_definitions[$parameter_number])) {
                    $parameter_name = $param_definitions[$parameter_number]->getName();
                }

                self::$testcase->assertEquals(array_shift($expected_values), $parameter_value, $parameter_name . ' parameter does not have the expected value for the method that is a mock of ' . $classname . '::' . $func . '()');
            }
        }
    }
}
