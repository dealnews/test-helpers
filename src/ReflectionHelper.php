<?php

namespace DealNews\TestHelpers;

trait ReflectionHelper {
    /**
     * Executes a method on an object that is not public
     *
     * @param   object  $object
     * @param   string  $method_name
     * @param   array   $arguments
     *
     * @return  mixed
     *
     * @throws \ReflectionException
     */
    protected function executeNonPublicMethod(object $object, string $method_name, array $arguments = []) {
        $class  = new \ReflectionClass($object);
        $method = $class->getMethod($method_name);
        $method->setAccessible(true);
        if (empty($arguments)) {
            return $method->invoke($object);
        } else {
            return $method->invokeArgs($object, $arguments);
        }
    }

    /**
     * Returns the value of a property on the object that is not a public property.
     *
     * If the property has not been initialized, then this method will return NULL
     *
     * @param   object  $object
     * @param   string  $property_name
     *
     * @return  mixed|null
     *
     * @throws \ReflectionException
     */
    protected function getNonPublicPropertyValue(object $object, string $property_name) {
        $class    = new \ReflectionClass($object);
        $property = $class->getProperty($property_name);
        $property->setAccessible(true);
        if ($property->isInitialized($object)) {
            return $property->getValue($object);
        } else {
            return null;
        }
    }

    /**
     * Sets the value of a property on the object that is not a public property
     *
     * @param   object  $object
     * @param   string  $property_name
     * @param   mixed   $property_value
     *
     * @return  void
     *
     * @throws  \ReflectionException
     */
    protected function setNonPublicPropertyValue(object $object, string $property_name, $property_value) {
        $class    = new \ReflectionClass($object);
        $property = $class->getProperty($property_name);
        $property->setAccessible(true);
        $property->setValue($object, $property_value);
    }
}
