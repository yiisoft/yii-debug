<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug as D;
use Yiisoft\Yii\Debug\Dumper;

final class DumperTest extends TestCase
{
    /**
     * @dataProvider asJsonObjectMapDataProvider
     *
     * @param string $expectedResult
     *
     * @group JOM
     */
    public function testAsJsonObjectsMap(mixed $var, $expectedResult): void
    {
        $exportResult = Dumper::create($var)->asJsonObjectsMap();
        $this->assertEquals($expectedResult, $exportResult);
    }

    public function asJsonObjectMapDataProvider(): array
    {
        $user = new stdClass();
        $user->id = 1;
        $objectId = spl_object_id($user);

        $decoratedUser = clone $user;
        $decoratedUser->name = 'Name';
        $decoratedUser->originalUser = $user;
        $decoratedObjectId = spl_object_id($decoratedUser);

        return [
            [
                $user,
                <<<S
                [{"stdClass#{$objectId}":{"public \$id":1}}]
                S,
            ],
            [
                $decoratedUser,
                <<<S
                [{"stdClass#{$decoratedObjectId}":{"public \$id":1,"public \$name":"Name","public \$originalUser":"object@stdClass#{$objectId}"}},{"stdClass#{$objectId}":{"public \$id":1}}]
                S,
            ],
        ];
    }

    /**
     * @dataProvider jsonDataProvider()
     */
    public function testAsJson($variable, string $result): void
    {
        $output = Dumper::create($variable)->asJson();
        $this->assertEqualsWithoutLE($result, $output);
    }

    public function jsonDataProvider(): array
    {
        $objectWithClosureInProperty = new stdClass();
        // @formatter:off
        $objectWithClosureInProperty->a = fn () => 1;
        // @formatter:on
        $objectWithClosureInPropertyId = spl_object_id($objectWithClosureInProperty);
        $objectWithClosureInPropertyClosureId = spl_object_id($objectWithClosureInProperty->a);

        $emptyObject = new stdClass();
        $emptyObjectId = spl_object_id($emptyObject);

        // @formatter:off
        $shortFunctionObject = fn () => 1;
        // @formatter:on
        $shortFunctionObjectId = spl_object_id($shortFunctionObject);

        // @formatter:off
        $staticShortFunctionObject = static fn () => 1;
        // @formatter:on
        $staticShortFunctionObjectId = spl_object_id($staticShortFunctionObject);

        // @formatter:off
        $functionObject = fn() => 1;
        // @formatter:on
        $functionObjectId = spl_object_id($functionObject);

        // @formatter:off
        $staticFunctionObject = static fn() => 1;
        // @formatter:on
        $staticFunctionObjectId = spl_object_id($staticFunctionObject);

        // @formatter:off
        $closureWithNullCollisionOperatorObject = fn () => $_ENV['var'] ?? null;
        // @formatter:on
        $closureWithNullCollisionOperatorObjectId = spl_object_id($closureWithNullCollisionOperatorObject);

        // @formatter:off
        $closureWithUsualClassNameObject = fn (Dumper $date) => new \DateTimeZone('');
        // @formatter:on
        $closureWithUsualClassNameObjectId = spl_object_id($closureWithUsualClassNameObject);

        // @formatter:off
        $closureWithAliasedClassNameObject = fn (Dumper $date) => new \DateTimeZone('');
        // @formatter:on
        $closureWithAliasedClassNameObjectId = spl_object_id($closureWithAliasedClassNameObject);

        // @formatter:off
        $closureWithAliasedNamespaceObject = fn (D\Dumper $date) => new \DateTimeZone('');
        // @formatter:on
        $closureWithAliasedNamespaceObjectId = spl_object_id($closureWithAliasedNamespaceObject);

        // @formatter:off
        $closureInArrayObject = fn () => new \DateTimeZone('');
        // @formatter:on
        $closureInArrayObjectId = spl_object_id($closureInArrayObject);

        return [
            'empty object' => [
                $emptyObject,
                <<<S
                {"stdClass#{$emptyObjectId}":"{stateless object}"}
                S,
            ],
            'short function' => [
                $shortFunctionObject,
                <<<S
                {"Closure#{$shortFunctionObjectId}":"fn () => 1"}
                S,
            ],
            'short static function' => [
                $staticShortFunctionObject,
                <<<S
                {"Closure#{$staticShortFunctionObjectId}":"static fn () => 1"}
                S,
            ],
            'function' => [
                $functionObject,
                <<<S
                {"Closure#{$functionObjectId}":"function () {\\n            return 1;\\n        }"}
                S,
            ],
            'static function' => [
                $staticFunctionObject,
                <<<S
                {"Closure#{$staticFunctionObjectId}":"static function () {\\n            return 1;\\n        }"}
                S,
            ],
            'string' => [
                'Hello, Yii!',
                '"Hello, Yii!"',
            ],
            'empty string' => [
                '',
                '""',
            ],
            'null' => [
                null,
                'null',
            ],
            'integer' => [
                1,
                '1',
            ],
            'integer with separator' => [
                1_23_456,
                '123456',
            ],
            'boolean' => [
                true,
                'true',
            ],
            'resource' => [
                fopen('php://input', 'rb'),
                '{"timed_out":false,"blocked":true,"eof":false,"wrapper_type":"PHP","stream_type":"Input","mode":"rb","unread_bytes":0,"seekable":true,"uri":"php:\/\/input"}',
            ],
            'empty array' => [
                [],
                '[]',
            ],
            'array of 3 elements, automatic keys' => [
                [
                    'one',
                    'two',
                    'three',
                ],
                '["one","two","three"]',
            ],
            'array of 3 elements, custom keys' => [
                [
                    2 => 'one',
                    'two' => 'two',
                    0 => 'three',
                ],
                '{"2":"one","two":"two","0":"three"}',
            ],
            'closure in array' => [
                // @formatter:off
                [$closureInArrayObject],
                // @formatter:on
                <<<S
                [{"Closure#{$closureInArrayObjectId}":"fn () => new \\\DateTimeZone('')"}]
                S,
            ],
            'original class name' => [
                $closureWithUsualClassNameObject,
                <<<S
                {"Closure#{$closureWithUsualClassNameObjectId}":"fn (\\\Yiisoft\\\Yii\\\Debug\\\Dumper \$date) => new \\\DateTimeZone('')"}
                S,
            ],
            'class alias' => [
                $closureWithAliasedClassNameObject,
                <<<S
                {"Closure#{$closureWithAliasedClassNameObjectId}":"fn (\\\Yiisoft\\\Yii\\\Debug\\\Dumper \$date) => new \\\DateTimeZone('')"}
                S,
            ],
            'namespace alias' => [
                $closureWithAliasedNamespaceObject,
                <<<S
                {"Closure#{$closureWithAliasedNamespaceObjectId}":"fn (\\\Yiisoft\\\Yii\\\Debug\\\Dumper \$date) => new \\\DateTimeZone('')"}
                S,
            ],
            'closure with null-collision operator' => [
                $closureWithNullCollisionOperatorObject,
                <<<S
                {"Closure#{$closureWithNullCollisionOperatorObjectId}":"fn () => \$_ENV['var'] ?? null"}
                S,
            ],
            'utf8 supported' => [
                '🤣',
                '"🤣"',
            ],
            'closure in property supported' => [
                $objectWithClosureInProperty,
                <<<S
                {"stdClass#{$objectWithClosureInPropertyId}":{"public \$a":{"Closure#{$objectWithClosureInPropertyClosureId}":"fn () => 1"}}}
                S,
            ],
            'binary string' => [
                pack('H*', md5('binary string')),
                '"ɍ��^��\u00191\u0017�]�-f�"',
            ],
        ];
    }

    /**
     * Asserting two strings equality ignoring line endings.
     */
    protected function assertEqualsWithoutLE(string $expected, string $actual, string $message = ''): void
    {
        $expected = str_replace(["\r\n", '\r\n'], ["\n", '\n'], $expected);
        $actual = str_replace(["\r\n", '\r\n'], ["\n", '\n'], $actual);
        $this->assertEquals($expected, $actual, $message);
    }
}
