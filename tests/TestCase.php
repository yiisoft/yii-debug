<?php

namespace Yiisoft\Yii\Debug\Tests;

use ReflectionMethod;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @TODO Remove
     *
     * @param $object
     * @param string $methodName
     * @return mixed
     * @throws \ReflectionException
     * @deprecated Don't call protected methods
     */
    protected function invokeMethod($object, string $methodName)
    {
        $method = new ReflectionMethod(get_class($object), $methodName);
        $method->setAccessible(true);

        return $method->invoke($object);
    }

}
