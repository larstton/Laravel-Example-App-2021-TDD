<?php

namespace Tests\Concerns;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

trait AccessNonPublic
{
    protected function callProtectedMethodOn(string $class, string $method, ...$params)
    {
        try {
            $method = $this->getProtectedMethod($class, $method);
        } catch (ReflectionException $e) {
            //
        }

        return $method->invokeArgs(resolve($class), $params ?? []);
    }

    /**
     * @param  string  $className
     * @param  string  $methodName
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    protected function getProtectedMethod(string $className, string $methodName)
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    protected function getPrivateProperty($className, $propertyName)
    {
        try {
            $reflector = new ReflectionClass($className);
            $property = $reflector->getProperty($propertyName);
            $property->setAccessible(true);

            return $property;
        } catch (ReflectionException $e) {
            //
        }

        return false;
    }
}
