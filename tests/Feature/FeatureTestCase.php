<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ReflectionClass;

abstract class FeatureTestCase extends TestCase
{
    protected function callPrivateMethod(string $methodName, Object $class, ...$args): mixed
    {
        $reflectionClass = new ReflectionClass($class);
        $method = $reflectionClass->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invoke($class, ...$args);
    }

    protected function setPrivateProperty(string $propName, mixed $value, Object $class): void
    {
        $reflectionClass = new ReflectionClass($class);
        $prop = $reflectionClass->getProperty($propName);
        $prop->setAccessible(true);
        $prop->setValue($class, $value);
    }

}
