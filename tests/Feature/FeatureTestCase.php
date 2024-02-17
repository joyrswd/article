<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ReflectionClass;

abstract class FeatureTestCase extends TestCase
{
    protected function callPrivate($methodName, $className, ...$args): mixed
    {
        $class = app($className);
        $reflectionClass = new ReflectionClass($class);
        $method = $reflectionClass->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invoke($class, ...$args);
    }
}
