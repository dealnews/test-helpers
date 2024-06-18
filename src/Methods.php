<?php

namespace DealNews\TestHelpers;

/**
 * Trait for mocking return values in Mock objects
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @author      Jeremy Earle <jearle@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     \DealNews\TestHelpers
 */
trait Methods {
    public array $method_counts               = [];
    public static array $static_method_counts = [];

    protected array $stacks               = [];
    protected static array $static_stacks = [];

    protected array $return_values               = [];
    protected static array $static_return_values = [];

    /**
     * Resets the stacks and method counts, then adds a stack of return values for each method mentioned
     * in the provided array
     *
     * @param array $stacks
     */
    public function _addMultipleMethodResponses(array $stacks) {
        $this->stacks        = [];
        $this->method_counts = [];
        foreach ($stacks as $func => $stack) {
            $this->_addMultipleResponses($func, $stack);
        }
    }

    /**
     * Sets the stack/list of return values for the provided method
     *
     * @param   string  $func       The name of the method
     * @param   array   $return     A list/stack of return values you want the method to return
     */
    public function _addMultipleResponses(string $func, array $return) {
        self::_checkMethodExists($func);
        $this->stacks[$func] = $return;
    }

    /**
     * Adds a specific value that should be returned when the provided param values are given to
     * the provided method name.
     *
     * This is not a stack of return values like _addMultipleResponses or _addMultipleMethodResponses uses. And once the
     * method is called with the expected parameters, it will return your provided return value for that method call and
     * each subsequent call with those parameter values.
     *
     * @param   string  $func       The name of the method
     * @param   array   $params     A list of the parameter values that are expected that should yield this return value
     * @param   mixed   $return     The return value
     */
    public function _addReturnValueWithParams(string $func, array $params, $return) {
        self::_checkMethodExists($func);
        $this->return_values[$func][serialize($params)] = $return;
    }

    /**
     * Resets the stacks, counts, etc... associated with static method calls. Then adds a stack of return values for each
     * static method mentioned in the provided array
     *
     * @param array $stacks
     */
    public static function _addMultipleStaticMethodResponses(array $stacks) {
        self::_resetStaticResponses();
        foreach ($stacks as $func => $stack) {
            self::_addMultipleStaticResponses($func, $stack);
        }
    }

    /**
     * Sets the stack/list of return values for the provided static method
     *
     * @param   string  $func       The name of the method
     * @param   array   $return     A list/stack of return values you want the method to return
     */
    public static function _addMultipleStaticResponses(string $func, array $return) {
        self::_checkMethodExists($func);
        self::$static_stacks[$func] = $return;
    }

    /**
     * Resets static properties associated with static method stacks, counts, etc...
     *
     * Should be called when calling static methods at the top of each test.
     */
    public static function _resetStaticResponses() {
        self::$static_stacks        = [];
        self::$static_method_counts = [];
        self::$static_return_values = [];
    }

    /**
     * Adds a specific value that should be returned when the provided param values are given to
     * the provided static method name.
     *
     * This is not a stack of return values like _addMultipleStaticResponses or _addMultipleStaticMethodResponses uses. And once the
     * static method is called with the expected parameters, it will return your provided return value for that method call and
     * each subsequent call with those parameter values.
     *
     * @param   string  $func       The name of the method
     * @param   array   $params     A list of the parameter values that are expected that should yield this return value
     * @param   mixed   $return     The return value
     */
    public static function _addStaticReturnValueWithParams(string $func, array $params, $return) {
        self::_checkMethodExists($func);
        self::$static_return_values[$func][serialize($params)] = $return;
    }

    /**
     * Checks to make sure that the provided method name exists in the parent class. This assumes that your mock class extends
     * a class (the parent). If not, the class itself will be checked that it contains the method.
     *
     * @param   string      $func       The method name
     */
    protected static function _checkMethodExists(string $func) {
        $called_class = get_called_class();
        $parent       = get_parent_class($called_class);

        // Sometimes a mock class will implement and interface instead
        // of extending a class. In that case,
        if (empty($parent)) {
            $interfaces = class_implements($called_class);
            if (empty($interfaces)) {
                throw new \LogicException("$called_class must extend another class or implement an interface which defines $func to add a mock response with the Mock\\Methods trait.");
            }
            foreach ($interfaces as $interface) {
                if (method_exists($interface, $func)) {
                    $parent = $interface;
                    break;
                }
            }
        }

        if (empty($parent) || !method_exists($parent, $func)) {
            if (empty($parent)) {
                $parent = $called_class;
            }
            throw new \InvalidArgumentException("Method $func not found for $parent when attempting to add a mock response with the Mock\\Methods trait.");
        }
    }

    /**
     * Pulls the next static method response/return value off of the stack and returns it for the provided static method
     * name. It also increments the static method count.
     *
     * @param   string      $func       The name of the method
     * @param   mixed       $default    A default value to use if there are no return values on the stack
     *
     * @return  mixed|null
     */
    protected static function _getNextStaticResponse(string $func, $default) {
        self::_incrementCount($func, self::$static_method_counts);

        return empty(self::$static_stacks[$func]) ? $default : array_shift(self::$static_stacks[$func]);
    }

    /**
     * Pulls the next method response/return value off of the stack and returns it for the provided method name. It also
     * increments the method count.
     *
     * @param   string      $func       The name of the method
     * @param   mixed       $default    A default value to use of there are no return values on the stack
     *
     * @return  mixed|null
     */
    protected function _getNextResponse(string $func, $default) {
        self::_incrementCount($func, $this->method_counts);

        return empty($this->stacks[$func]) ? $default : array_shift($this->stacks[$func]);
    }

    /**
     * Checks to see if there is a return value set that matched the provided parameter values. If there is, it returns it.
     * Otherwise, it returns the default value provided.
     *
     * Also, increments the method counts.
     *
     * @param   string      $func       The name of the method
     * @param   array       $params     A list of parameter values that were passed to the method name provided
     * @param   mixed|null  $default    A default value to use if there is no matched return value for this set of parameter values
     *
     * @return  mixed|null
     */
    protected function _getResponseWithParams(string $func, array $params, $default = null) {
        self::_incrementCount($func, $this->method_counts);
        $ser = serialize($params);

        return (
            array_key_exists($func, $this->return_values) &&
            array_key_exists($ser, $this->return_values[$func])
        ) ? $this->return_values[$func][$ser] : $default;
    }

    /**
     * Checks to see if there is a return value set that matched the provided parameter values. If there is, it returns it.
     * Otherwise, it returns the default value provided.
     *
     * Also, increments the static method counts.
     *
     * @param   string      $func       The name of the method
     * @param   array       $params     A list of parameter values that were passed to the method name provided
     * @param   mixed|null  $default    A default value to use if there is no matched return value for this set of parameter values
     *
     * @return  mixed|null
     */
    protected static function _getStaticResponseWithParams(string $func, array $params, $default = null) {
        self::_incrementCount($func, self::$static_method_counts);

        $ser = serialize($params);

        return (
            array_key_exists($func, self::$static_return_values) &&
            array_key_exists($ser, self::$static_return_values[$func])
        ) ? self::$static_return_values[$func][$ser] : $default;
    }

    /**
     * Increments the count for the provided method stored in the provided variable
     *
     * @param   string  $func           The name of the method
     * @param   array   $stack_count    The array that the method count is stored in
     */
    protected static function _incrementCount(string $func, array &$stack_count) {
        if (empty($stack_count[$func])) {
            $stack_count[$func] = 0;
        }

        $stack_count[$func]++;
    }
}
