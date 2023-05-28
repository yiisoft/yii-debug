<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit;

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

    public static function jsonDataProvider(): iterable
    {
        $emptyObject = new stdClass();
        $emptyObjectId = spl_object_id($emptyObject);

        yield 'empty object' => [
            $emptyObject,
            <<<S
                {"stdClass#{$emptyObjectId}":"{stateless object}"}
                S,
        ];

        // @formatter:off
        $shortFunctionObject = fn () => 1;
        // @formatter:on
        $shortFunctionObjectId = spl_object_id($shortFunctionObject);

        yield 'short function' => [
            $shortFunctionObject,
            <<<S
                {"Closure#{$shortFunctionObjectId}":"fn () => 1"}
                S,
        ];

        // @formatter:off
        $staticShortFunctionObject = static fn () => 1;
        // @formatter:on
        $staticShortFunctionObjectId = spl_object_id($staticShortFunctionObject);

        yield 'short static function' => [
            $staticShortFunctionObject,
            <<<S
                {"Closure#{$staticShortFunctionObjectId}":"static fn () => 1"}
                S,
        ];

        // @formatter:off
        $functionObject = function () {
            return 1;
        };
        // @formatter:on
        $functionObjectId = spl_object_id($functionObject);

        yield 'function' => [
            $functionObject,
            <<<S
                {"Closure#{$functionObjectId}":"function () {\\n            return 1;\\n        }"}
                S,
        ];

        // @formatter:off
        $staticFunctionObject = static function () {
            return 1;
        };
        // @formatter:on
        $staticFunctionObjectId = spl_object_id($staticFunctionObject);

        yield 'static function' => [
            $staticFunctionObject,
            <<<S
                {"Closure#{$staticFunctionObjectId}":"static function () {\\n            return 1;\\n        }"}
                S,
        ];
        yield 'string' => [
            'Hello, Yii!',
            '"Hello, Yii!"',
        ];
        yield 'empty string' => [
            '',
            '""',
        ];
        yield 'null' => [
            null,
            'null',
        ];
        yield 'integer' => [
            1,
            '1',
        ];
        yield 'integer with separator' => [
            1_23_456,
            '123456',
        ];
        yield 'boolean' => [
            true,
            'true',
        ];
        yield 'fileResource' => [
            fopen('php://input', 'rb'),
            '{"timed_out":false,"blocked":true,"eof":false,"wrapper_type":"PHP","stream_type":"Input","mode":"rb","unread_bytes":0,"seekable":true,"uri":"php:\/\/input"}',
        ];
        yield 'empty array' => [
            [],
            '[]',
        ];
        yield 'array of 3 elements, automatic keys' => [
            [
                'one',
                'two',
                'three',
            ],
            '["one","two","three"]',
        ];
        yield 'array of 3 elements, custom keys' => [
            [
                2 => 'one',
                'two' => 'two',
                0 => 'three',
            ],
            '{"2":"one","two":"two","0":"three"}',
        ];

        // @formatter:off
        $closureInArrayObject = fn () => new \DateTimeZone('');
        // @formatter:on
        $closureInArrayObjectId = spl_object_id($closureInArrayObject);

        yield 'closure in array' => [
            // @formatter:off
                [$closureInArrayObject],
                // @formatter:on
            <<<S
                [{"Closure#{$closureInArrayObjectId}":"fn () => new \\\DateTimeZone('')"}]
                S,
        ];

        // @formatter:off
        $closureWithUsualClassNameObject = fn (Dumper $date) => new \DateTimeZone('');
        // @formatter:on
        $closureWithUsualClassNameObjectId = spl_object_id($closureWithUsualClassNameObject);

        yield 'original class name' => [
            $closureWithUsualClassNameObject,
            <<<S
                {"Closure#{$closureWithUsualClassNameObjectId}":"fn (\\\Yiisoft\\\Yii\\\Debug\\\Dumper \$date) => new \\\DateTimeZone('')"}
                S,
        ];

        // @formatter:off
        $closureWithAliasedClassNameObject = fn (Dumper $date) => new \DateTimeZone('');
        // @formatter:on
        $closureWithAliasedClassNameObjectId = spl_object_id($closureWithAliasedClassNameObject);

        yield 'class alias' => [
            $closureWithAliasedClassNameObject,
            <<<S
                {"Closure#{$closureWithAliasedClassNameObjectId}":"fn (\\\Yiisoft\\\Yii\\\Debug\\\Dumper \$date) => new \\\DateTimeZone('')"}
                S,
        ];

        // @formatter:off
        $closureWithAliasedNamespaceObject = fn (D\Dumper $date) => new \DateTimeZone('');
        // @formatter:on
        $closureWithAliasedNamespaceObjectId = spl_object_id($closureWithAliasedNamespaceObject);

        yield 'namespace alias' => [
            $closureWithAliasedNamespaceObject,
            <<<S
                {"Closure#{$closureWithAliasedNamespaceObjectId}":"fn (\\\Yiisoft\\\Yii\\\Debug\\\Dumper \$date) => new \\\DateTimeZone('')"}
                S,
        ];
        // @formatter:off
        $closureWithNullCollisionOperatorObject = fn () => $_ENV['var'] ?? null;
        // @formatter:on
        $closureWithNullCollisionOperatorObjectId = spl_object_id($closureWithNullCollisionOperatorObject);

        yield 'closure with null-collision operator' => [
            $closureWithNullCollisionOperatorObject,
            <<<S
                {"Closure#{$closureWithNullCollisionOperatorObjectId}":"fn () => \$_ENV['var'] ?? null"}
                S,
        ];
        yield 'utf8 supported' => [
            '🤣',
            '"🤣"',
        ];


        $objectWithClosureInProperty = new stdClass();
        // @formatter:off
        $objectWithClosureInProperty->a = fn () => 1;
        // @formatter:on
        $objectWithClosureInPropertyId = spl_object_id($objectWithClosureInProperty);
        $objectWithClosureInPropertyClosureId = spl_object_id($objectWithClosureInProperty->a);

        yield 'closure in property supported' => [
            $objectWithClosureInProperty,
            <<<S
                {"stdClass#{$objectWithClosureInPropertyId}":{"public \$a":{"Closure#{$objectWithClosureInPropertyClosureId}":"fn () => 1"}}}
                S,
        ];
        yield 'binary string' => [
            pack('H*', md5('binary string')),
            '"ɍ��^��\u00191\u0017�]�-f�"',
        ];


        $fileResource = tmpfile();
        $fileResourceUri = preg_quote(stream_get_meta_data($fileResource)['uri'], '/');
        if (DIRECTORY_SEPARATOR === '\\') {
            $fileResourceUri = preg_quote($fileResourceUri, '.');
        }

        yield 'file resource' => [
            $fileResource,
            <<<S
                {"timed_out":false,"blocked":true,"eof":false,"wrapper_type":"plainfile","stream_type":"STDIO","mode":"r+b","unread_bytes":0,"seekable":true,"uri":"{$fileResourceUri}"}
                S,
        ];

        $closedFileResource = tmpfile();
        fclose($closedFileResource);

        yield 'closed file resource' => [
            $closedFileResource,
            '"{closed resource}"',
        ];

        $opendirResource = opendir('/tmp');

        yield 'opendir resource' => [
            $opendirResource,
            <<<S
                {"timed_out":false,"blocked":true,"eof":false,"wrapper_type":"plainfile","stream_type":"dir","mode":"r","unread_bytes":0,"seekable":true}
                S,
        ];

        $curlResource = curl_init('https://example.com');
        $curlResourceObjectId = spl_object_id($curlResource);;

        yield 'curl resource' => [
            $curlResource,
            <<<S
                {"CurlHandle#{$curlResourceObjectId}":"{stateless object}"}
                S,
        ];
        yield 'stdout' => [
            STDOUT,
            <<<S
                {"timed_out":false,"blocked":true,"eof":false,"wrapper_type":"PHP","stream_type":"STDIO","mode":"wb","unread_bytes":0,"seekable":false,"uri":"php:\/\/stdout"}
                S,
        ];
        yield 'stderr' => [
            STDERR,
            <<<S
                {"timed_out":false,"blocked":true,"eof":false,"wrapper_type":"PHP","stream_type":"STDIO","mode":"wb","unread_bytes":0,"seekable":false,"uri":"php:\/\/stderr"}
                S,
        ];
        yield 'stdin' => [
            STDIN,
            <<<S
                {"timed_out":false,"blocked":true,"eof":false,"wrapper_type":"PHP","stream_type":"STDIO","mode":"rb","unread_bytes":0,"seekable":false,"uri":"php:\/\/stdin"}
                S,
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
