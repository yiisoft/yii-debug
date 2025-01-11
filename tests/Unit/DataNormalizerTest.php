<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit;

use DateTimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug as D;
use Yiisoft\Yii\Debug\DataNormalizer;
use Yiisoft\Yii\Debug\Tests\Support\Stub\ThreeProperties;

use function socket_create;
use function sprintf;

use const AF_INET;
use const SOCK_STREAM;
use const SOL_TCP;

final class DataNormalizerTest extends TestCase
{
    public function testPrepareDataAndObjectsMapLevelOne(): void
    {
        $object = new stdClass();
        $object->var = 'test';
        $objectId = spl_object_id($object);

        [$data, $objectsMap] = (new DataNormalizer())->prepareDataAndObjectsMap([$object], 1);
        $this->assertSame(
            ["object@stdClass#$objectId"],
            $data,
        );
        $this->assertSame(
            [
                "stdClass#$objectId" => [
                    'public $var' => 'test',
                ],
            ],
            $objectsMap,
        );
    }

    public function testPrepareDataAndObjectsMapNestedObject(): void
    {
        $nested2 = new stdClass();
        $nested2->name = 'nested2';
        $nested2Id = spl_object_id($nested2);

        $nested1 = new stdClass();
        $nested1->name = 'nested1';
        $nested1->var = $nested2;
        $nested1Id = spl_object_id($nested1);

        $object = new stdClass();
        $object->name = 'root';
        $object->var = $nested1;
        $objectId = spl_object_id($object);

        [$data, $objectsMap] = (new DataNormalizer())->prepareDataAndObjectsMap([$object], 1);
        $this->assertSame(
            [
                "object@stdClass#$objectId",
            ],
            $data,
        );
        $this->assertSame(
            [
                "stdClass#$objectId" => [
                    'public $name' => 'root',
                    'public $var' => "object@stdClass#$nested1Id",
                ],
                "stdClass#$nested1Id" => [
                    'public $name' => 'nested1',
                    'public $var' => "object@stdClass#$nested2Id",
                ],
                "stdClass#$nested2Id" => [
                    'public $name' => 'nested2',
                ],
            ],
            $objectsMap,
        );
    }

    public function testPrepareDataAndObjectsMapArrayWithObject(): void
    {
        $nested2 = new stdClass();
        $nested2->name = 'nested2';
        $nested2Id = spl_object_id($nested2);

        $nested1 = new stdClass();
        $nested1->name = 'nested1';
        $nested1->var = [$nested2];
        $nested1Id = spl_object_id($nested1);

        $object = new stdClass();
        $object->name = 'root';
        $object->var = $nested1;
        $objectId = spl_object_id($object);

        [$data, $objectsMap] = (new DataNormalizer())->prepareDataAndObjectsMap([$object], 0);

        $this->assertSame('array (1 item) [...]', $data);
        $this->assertSame(
            [
                "stdClass#$objectId" => [
                    'public $name' => 'root',
                    'public $var' => "object@stdClass#$nested1Id",
                ],
                "stdClass#$nested1Id" => [
                    'public $name' => 'nested1',
                    'public $var' => 'array (1 item) [...]',
                ],
                "stdClass#$nested2Id" => [
                    'public $name' => 'nested2',
                ],
            ],
            $objectsMap,
        );
    }

    #[DataProvider('loopAsObjectMapDataProvider')]
    public function testLoopAsObjectsMap(mixed $var, int $depth, $expectedResult): void
    {
        [, $objectsMap] = (new DataNormalizer())->prepareDataAndObjectsMap([$var], $depth);
        $this->assertEquals($expectedResult, $objectsMap);
    }

    public static function loopAsObjectMapDataProvider(): iterable
    {
        // parent->child->parent structure
        $nested1 = new stdClass();
        $nested1->id = 'nested1';
        $nested2 = new stdClass();
        $nested2->id = 'nested2';
        $nested2->nested1 = $nested1;
        $nested1->nested2 = $nested2;

        $nested1Id = spl_object_id($nested1);
        $nested2Id = spl_object_id($nested2);

        // 5 is a min level to reproduce buggy dumping of parent->child->parent structure
        [$object1, $ids1] = self::getNested(5, $nested1);
        yield 'nested loop - object' => [
            $object1,
            5,
            [
                "stdClass#{$ids1[0]}" => [
                    "public \$id" => "lvl0",
                    "public \$lvl1" => "object@stdClass#{$ids1[1]}"
                ],
                "stdClass#{$ids1[1]}" => [
                    "public \$id" => "lvl1",
                    "public \$lvl2" => "object@stdClass#{$ids1[2]}"
                ],
                "stdClass#{$ids1[2]}" => [
                    "public \$id" => "lvl2",
                    "public \$lvl3" => "object@stdClass#{$ids1[3]}"
                ],
                "stdClass#{$ids1[3]}" => [
                    "public \$id" => "lvl3",
                    "public \$lvl4" => "object@stdClass#{$ids1[4]}"
                ],
                "stdClass#{$ids1[4]}" => [
                    "public \$id" => "lvl4",
                    "public \$lvl5" => "object@stdClass#{$nested1Id}"
                ],
                "stdClass#{$nested1Id}" => [
                    "public \$id" => "nested1",
                    "public \$nested2" => "object@stdClass#{$nested2Id}"
                ],
                "stdClass#{$nested2Id}" => [
                    "public \$id" => "nested2",
                    "public \$nested1" => "object@stdClass#{$nested1Id}"
                ]
            ],
        ];

        // array loop must be 1 level deeper to parse loop objects
        [$object2, $ids2] = self::getNested(6, [$nested1, $nested2]);
        yield 'nested loop - array' => [
            $object2,
            6,
            [
                "stdClass#$ids2[0]" => [
                    "public \$id" => "lvl0",
                    "public \$lvl1" => "object@stdClass#$ids2[1]"
                ],
                "stdClass#$ids2[1]" => [
                    "public \$id" => "lvl1",
                    "public \$lvl2" => "object@stdClass#$ids2[2]"
                ],
                "stdClass#$ids2[2]" => [
                    "public \$id" => "lvl2",
                    "public \$lvl3" => "object@stdClass#$ids2[3]"
                ],
                "stdClass#$ids2[3]" => [
                    "public \$id" => "lvl3",
                    "public \$lvl4" => "object@stdClass#$ids2[4]"
                ],
                "stdClass#$ids2[4]" => [
                    "public \$id" => "lvl4",
                    "public \$lvl5" => "object@stdClass#$ids2[5]"
                ],
                "stdClass#$ids2[5]" => [
                    "public \$id" => "lvl5",
                    "public \$lvl6" => [
                        "object@stdClass#$nested1Id",
                        "object@stdClass#$nested2Id"
                    ]
                ],
                "stdClass#$nested1Id" => [
                    "public \$id" => "nested1",
                    "public \$nested2" => "object@stdClass#$nested2Id"
                ],
                "stdClass#$nested2Id" => [
                    "public \$id" => "nested2",
                    "public \$nested1" => "object@stdClass#$nested1Id"
                ]
            ],
        ];

        // nested loop to inner array
        $object3 = new stdClass();
        $object3->id = 'lvl0';
        $object3->lv11 = [
            'id' => 'lvl1',
            'loop' => $nested1,
        ];
        $object3Id = spl_object_id($object3);

        yield 'nested loop to object->array' => [
            $object3,
            3,
            [
                "stdClass#$object3Id" => [
                    "public \$id" => "lvl0",
                    "public \$lv11" => [
                        "id" => "lvl1",
                        "loop" => "object@stdClass#$nested1Id"
                    ]
                ],
                "stdClass#$nested1Id" => [
                    "public \$id" => "nested1",
                    "public \$nested2" => "object@stdClass#$nested2Id"
                ],
                "stdClass#$nested2Id" => [
                    "public \$id" => "nested2",
                    "public \$nested1" => "object@stdClass#$nested1Id"
                ]
            ],
        ];
    }

    private static function getNested(int $depth, mixed $data): array
    {
        $objectIds = [];
        $head = $lvl = new stdClass();
        $objectIds[] = spl_object_id($head);
        $lvl->id = 'lvl0';

        for ($i = 1; $i < $depth; $i++) {
            $nested = new stdClass();
            $nested->id = 'lvl' . $i;
            $lvl->{'lvl' . $i} = $nested;
            $lvl = $nested;
            $objectIds[] = spl_object_id($nested);
        }
        $lvl->{'lvl' . $i} = $data;

        return [$head, $objectIds];
    }

    public function testObjectExpanding(): void
    {
        $var = $this->createNested(10, [[[[[[[[['key' => 'end']]]]]]]]]);

        $lvl1Id = spl_object_id($var);
        $lvl2Id = spl_object_id($var->prop1);
        $lvl3Id = spl_object_id($var->prop1->prop1);
        $lvl4Id = spl_object_id($var->prop1->prop1->prop1);
        $lvl5Id = spl_object_id($var->prop1->prop1->prop1->prop1);
        $lvl6Id = spl_object_id($var->prop1->prop2->prop1->prop1->prop1);
        $lvl7Id = spl_object_id($var->prop1->prop2->prop1->prop1->prop1->prop1);
        $lvl8Id = spl_object_id($var->prop1->prop2->prop1->prop1->prop1->prop1->prop1);
        $lvl9Id = spl_object_id($var->prop1->prop2->prop1->prop1->prop1->prop1->prop1->prop1);
        $lvl10Id = spl_object_id($var->prop1->prop2->prop1->prop1->prop1->prop1->prop1->prop1->prop1);

        $expectedResult = [
            "stdClass#$lvl1Id" => [
                "public \$id" => "lvl1",
                "public \$prop1" => "object@stdClass#$lvl2Id",
                "public \$prop2" => "object@stdClass#$lvl2Id",
            ],
            "stdClass#$lvl2Id" => [
                "public \$id" => "lvl2",
                "public \$prop1" => "object@stdClass#$lvl3Id",
                "public \$prop2" => "object@stdClass#$lvl3Id",
            ],
            "stdClass#$lvl3Id" => [
                "public \$id" => "lvl3",
                "public \$prop1" => "object@stdClass#$lvl4Id",
                "public \$prop2" => "object@stdClass#$lvl4Id",
            ],
            "stdClass#$lvl4Id" => [
                "public \$id" => "lvl4",
                "public \$prop1" => "object@stdClass#$lvl5Id",
                "public \$prop2" => "object@stdClass#$lvl5Id",
            ],
            "stdClass#$lvl5Id" => [
                "public \$id" => "lvl5",
                "public \$prop1" => "object@stdClass#$lvl6Id",
                "public \$prop2" => "object@stdClass#$lvl6Id",
            ],
            "stdClass#$lvl6Id" => [
                "public \$id" => "lvl6",
                "public \$prop1" => "object@stdClass#$lvl7Id",
                "public \$prop2" => "object@stdClass#$lvl7Id",
            ],
            "stdClass#$lvl7Id" => [
                "public \$id" => "lvl7",
                "public \$prop1" => "object@stdClass#$lvl8Id",
                "public \$prop2" => "object@stdClass#$lvl8Id",
            ],
            "stdClass#$lvl8Id" => [
                "public \$id" => "lvl8",
                "public \$prop1" => "object@stdClass#$lvl9Id",
                "public \$prop2" => "object@stdClass#$lvl9Id",
            ],
            "stdClass#$lvl9Id" => [
                "public \$id" => "lvl9",
                "public \$prop1" => "object@stdClass#$lvl10Id",
                "public \$prop2" => "object@stdClass#$lvl10Id",
            ],
            "stdClass#$lvl10Id" => [
                "public \$id" => "lvl10",
                "public \$loop" => [
                    [
                        "array (1 item) [...]",
                ],
                ],
                "public \$head" => "object@stdClass#$lvl1Id",
            ],
        ];

        [, $objectsMap] = (new DataNormalizer())->prepareDataAndObjectsMap([$var], 2);

        $this->assertEquals($expectedResult, $objectsMap);
    }

    private function createNested(int $depth, mixed $data): object
    {
        $head = $lvl = new stdClass();
        $lvl->id = 'lvl1';

        for ($i = 2; $i <= $depth; $i++) {
            $nested = new stdClass();
            $nested->id = 'lvl' . $i;
            $lvl->prop1 = $nested;
            $lvl->prop2 = $nested;
            $lvl = $nested;
        }
        $lvl->loop = $data;
        $lvl->head = $head;

        return $head;
    }

    #[DataProvider('objectsMapDataProvider')]
    public function testObjectsMap(mixed $var, $expectedResult): void
    {
        [, $objectsMap] = (new DataNormalizer())->prepareDataAndObjectsMap([$var]);
        $this->assertEquals($expectedResult, $objectsMap);
    }

    public static function objectsMapDataProvider(): iterable
    {
        $user = new stdClass();
        $user->id = 1;
        $objectId = spl_object_id($user);

        yield 'flat std class' => [
            $user,
            [
                "stdClass#{$objectId}" => [
                    "public \$id" => 1,
                ],
            ],
        ];

        $decoratedUser = clone $user;
        $decoratedUser->name = 'Name';
        $decoratedUser->originalUser = $user;
        $decoratedObjectId = spl_object_id($decoratedUser);

        yield 'nested std class' => [
            $decoratedUser,
            [
                "stdClass#{$decoratedObjectId}" => [
                    "public \$id" => 1,
                    "public \$name" => "Name",
                    "public \$originalUser" => "object@stdClass#{$objectId}",
                ],
                "stdClass#{$objectId}" => [
                    "public \$id" => 1,
                ],
            ],
        ];

        $closureInsideObject = new stdClass();
        $closureObject = fn () => true;
        $closureInsideObject->closure = $closureObject;
        $closureInsideObjectId = spl_object_id($closureInsideObject);
        yield 'closure inside std class' => [
            $closureInsideObject,
            [
                "stdClass#{$closureInsideObjectId}" => [
                    "public \$closure" => "fn () => true"
                ]
            ],
        ];

        $socketResource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $socketResourceId = spl_object_id($socketResource);
        yield 'socket resource' => [
            $socketResource,
            ["Socket#{$socketResourceId}" => "{stateless object}"],
        ];

        $curlResource = curl_init('https://example.com');
        $curlResourceObjectId = spl_object_id($curlResource);
        yield 'curl resource' => [
            $curlResource,
            ["CurlHandle#{$curlResourceObjectId}" => "{stateless object}"],
        ];

        $emptyObject = new stdClass();
        $emptyObjectId = spl_object_id($emptyObject);
        yield 'empty object' => [
            $emptyObject,
            ["stdClass#{$emptyObjectId}" => "{stateless object}"],
        ];
    }

    #[DataProvider('prepareDataDataProvider')]
    public function testPrepareData(mixed $variable, mixed $result): void
    {
        $data = (new DataNormalizer())->prepareData([$variable]);
        $this->assertSame([$result], $data);
    }

    public function testCacheDoesNotCoversObjectOutOfDumpDepth(): void
    {
        $object1 = new stdClass();
        $object1Id = spl_object_id($object1);
        $object2 = new stdClass();
        $object2Id = spl_object_id($object2);

        $variable = [$object1, [[$object2]]];

        [$data, $objectsMap] = (new DataNormalizer())->prepareDataAndObjectsMap($variable, 2);
        $this->assertSame(
            [
                "object@stdClass#$object1Id",
                ['array (1 item) [...]'],
            ],
            $data,
        );
        $this->assertSame(
            [
                "stdClass#$object1Id" => "{stateless object}",
                "stdClass#$object2Id" => "{stateless object}",
            ],
            $objectsMap,
        );
    }

    public function testDepthLimitInObjectMap(): void
    {
        $variable = [1, []];

        [$data, $objectsMap] = (new DataNormalizer())->prepareDataAndObjectsMap($variable, 0);

        $this->assertSame('array (2 items) [...]', $data);
        $this->assertSame([], $objectsMap);
    }

    public function testObjectProvidesDebugInfoMethod(): void
    {
        $variable = new class () {
            public function __debugInfo(): array
            {
                return ['test' => 'ok'];
            }
        };
        $objectId = spl_object_id($variable);

        [$data, $objectsMap] = (new DataNormalizer())->prepareDataAndObjectsMap([$variable], 2);
        $this->assertSame(
            ["object@class@anonymous#{$objectId}"],
            $data,
        );
        $this->assertSame(
            [
                "class@anonymous#{$objectId}" => ['public $test' => 'ok'],
            ],
            $objectsMap,
        );
    }

    public function testStatelessObjectInlined(): void
    {
        $statelessObject = new stdClass();
        $statelessObjectId = spl_object_id($statelessObject);

        $statefulObject = new stdClass();
        $statefulObject->id = 1;
        $statefulObjectId = spl_object_id($statefulObject);

        $variable = [$statelessObject, [$statefulObject]];

        [$data, $objectsMap] = (new DataNormalizer())->prepareDataAndObjectsMap($variable, 3);
        $this->assertSame(
            ["object@stdClass#{$statelessObjectId}", ["object@stdClass#{$statefulObjectId}"]],
            $data,
        );
        $this->assertSame(
            [
                "stdClass#{$statelessObjectId}" => '{stateless object}',
                "stdClass#{$statefulObjectId}" => ['public $id' => 1],
            ],
            $objectsMap,
        );
    }

    #[DataProvider('dataDeepNestedArray')]
    public function testDeepNestedArray(array $variable, mixed $expectedResult): void
    {
        [$data,] = (new DataNormalizer())->prepareDataAndObjectsMap($variable, 2);
        $this->assertSame($expectedResult, $data);
    }

    public static function dataDeepNestedArray(): iterable
    {
        yield 'singular' => [
            [[['test']]],
            [['array (1 item) [...]']],
        ];

        yield 'plural' => [
            [[['test', 'test'], ['test']]],
            [['array (2 items) [...]', 'array (1 item) [...]']],
        ];
    }

    public function testDeepNestedObject(): void
    {
        $object = new ThreeProperties();
        $object->first = $object;
        $variable = [[$object]];

        $data = (new DataNormalizer())->prepareData($variable, 2);

        $this->assertSame(
            [
                [
                    sprintf(
                        '%s#%d (...)',
                        ThreeProperties::class,
                        spl_object_id($object),
                    ),
                ],
            ],
            $data,
        );
    }

    public function testObjectVisibilityProperties(): void
    {
        $variable = new ThreeProperties();

        [,$objectsMap] = (new DataNormalizer())->prepareDataAndObjectsMap([$variable]);
        $key = sprintf('%s#%d', ThreeProperties::class, spl_object_id($variable));
        $this->assertSame(
            [
                $key => [
                    "public \$first" => "first",
                    "protected \$second" => "second",
                    "private \$third" => "third"
                ],
            ],
            $objectsMap,
        );
    }

    public function testExcludedClasses(): void
    {
        $object1 = new stdClass();
        $object1Id = spl_object_id($object1);
        $object1Class = $object1::class;

        $object2 = new DateTimeZone('UTC');
        $object2Id = spl_object_id($object2);
        $object2Class = $object2::class;

        [$data,] = (new DataNormalizer([$object1Class]))->prepareDataAndObjectsMap([$object1, $object2], 2);

        $this->assertSame(
            [
                "{$object1Class}#{$object1Id} (...)",
                "object@{$object2Class}#{$object2Id}"
            ],
            $data,
        );
    }

    public static function prepareDataDataProvider(): iterable
    {
        // @formatter:off
        $shortFunctionObject = fn () => 1;
        // @formatter:on
        yield 'short function' => [
            $shortFunctionObject,
            'fn () => 1',
        ];

        // @formatter:off
        $staticShortFunctionObject = static fn () => 1;
        // @formatter:on

        yield 'short static function' => [
            $staticShortFunctionObject,
            'static fn () => 1',
        ];

        // @formatter:off
        $functionObject = function () {
            return 1;
        };
        // @formatter:on
        yield 'function' => [
            $functionObject,
            <<<STRING
            function () {
                return 1;
            }
            STRING,
        ];

        // @formatter:off
        $staticFunctionObject = static function () {
            return 1;
        };
        // @formatter:on
        yield 'static function' => [
            $staticFunctionObject,
            <<<STRING
            static function () {
                return 1;
            }
            STRING,
        ];
        yield 'string' => [
            'Hello, Yii!',
            'Hello, Yii!',
        ];
        yield 'empty string' => [
            '',
            '',
        ];
        yield 'null' => [
            null,
            null,
        ];
        yield 'integer' => [
            1,
            1,
        ];
        yield 'integer with separator' => [
            1_23_456,
            123456,
        ];
        yield 'boolean' => [
            true,
            true,
        ];
        yield 'fileResource' => [
            fopen('php://input', 'rb'),
            [
                "timed_out" => false,
                "blocked" => true,
                "eof" => false,
                "wrapper_type" => "PHP",
                "stream_type" => "Input",
                "mode" => "rb",
                "unread_bytes" => 0,
                "seekable" => true,
                "uri" => "php://input",
            ],
        ];
        yield 'empty array' => [
            [],
            [],
        ];
        yield 'array of 3 elements, automatic keys' => [
            [
                'one',
                'two',
                'three',
            ],
            [
                'one',
                'two',
                'three',
            ],
        ];
        yield 'array of 3 elements, custom keys' => [
            [
                2 => 'one',
                'two' => 'two',
                0 => 'three',
            ],
            [
                2 => 'one',
                'two' => 'two',
                0 => 'three',
            ],
        ];

        // @formatter:off
        $closureInArrayObject = fn () => new \DateTimeZone('');
        // @formatter:on
        yield 'closure in array' => [
            // @formatter:off
            [$closureInArrayObject],
            // @formatter:on
            ["fn () => new \\DateTimeZone('')"],
        ];

        // @formatter:off
        $closureWithUsualClassNameObject = fn (DataNormalizer $date) => new \DateTimeZone('');
        // @formatter:on
        yield 'original class name' => [
            $closureWithUsualClassNameObject,
            "fn (\\Yiisoft\\Yii\\Debug\\DataNormalizer \$date) => new \\DateTimeZone('')",
        ];

        // @formatter:off
        $closureWithAliasedClassNameObject = fn (DataNormalizer $date) => new \DateTimeZone('');
        // @formatter:on
        yield 'class alias' => [
            $closureWithAliasedClassNameObject,
            "fn (\\Yiisoft\\Yii\\Debug\\DataNormalizer \$date) => new \\DateTimeZone('')",
        ];

        // @formatter:off
        $closureWithAliasedNamespaceObject = fn (D\DataNormalizer $date) => new \DateTimeZone('');
        // @formatter:on
        yield 'namespace alias' => [
            $closureWithAliasedNamespaceObject,
            "fn (\\Yiisoft\\Yii\\Debug\\DataNormalizer \$date) => new \\DateTimeZone('')",
        ];
        // @formatter:off
        $closureWithNullCollisionOperatorObject = fn () => $_ENV['var'] ?? null;
        // @formatter:on
        yield 'closure with null-collision operator' => [
            $closureWithNullCollisionOperatorObject,
            "fn () => \$_ENV['var'] ?? null",
        ];
        yield 'utf8 supported' => [
            '🤣',
            '🤣',
        ];

        $string = pack('H*', md5('binary string'));
        yield 'binary string' => [
            $string,
            $string,
        ];

        $fileResource = tmpfile();
        $fileResourceUri = stream_get_meta_data($fileResource)['uri'];
        yield 'file resource' => [
            $fileResource,
            [
                "timed_out" => false,
                "blocked" => true,
                "eof" => false,
                "wrapper_type" => "plainfile",
                "stream_type" => "STDIO",
                "mode" => "r+b",
                "unread_bytes" => 0,
                "seekable" => true,
                "uri" => "{$fileResourceUri}",
            ],
        ];

        $closedFileResource = tmpfile();
        fclose($closedFileResource);

        yield 'closed file resource' => [
            $closedFileResource,
            '{closed resource}',
        ];

        $opendirResource = opendir(sys_get_temp_dir());
        yield 'opendir resource' => [
            $opendirResource,
            [
                "timed_out" => false,
                "blocked" => true,
                "eof" => false,
                "wrapper_type" => "plainfile",
                "stream_type" => "dir",
                "mode" => "r",
                "unread_bytes" => 0,
                "seekable" => true,
            ],
        ];

        yield 'stdout' => [
            STDOUT,
            [
                "timed_out" => false,
                "blocked" => true,
                "eof" => false,
                "wrapper_type" => "PHP",
                "stream_type" => "STDIO",
                "mode" => "wb",
                "unread_bytes" => 0,
                "seekable" => false,
                "uri" => "php://stdout",
            ],
        ];
        yield 'stderr' => [
            STDERR,
            [
                "timed_out" => false,
                "blocked" => true,
                "eof" => false,
                "wrapper_type" => "PHP",
                "stream_type" => "STDIO",
                "mode" => "wb",
                "unread_bytes" => 0,
                "seekable" => false,
                "uri" => "php://stderr",
            ],
        ];
        yield 'stdin' => [
            STDIN,
            [
                "timed_out" => false,
                "blocked" => true,
                "eof" => false,
                "wrapper_type" => "PHP",
                "stream_type" => "STDIO",
                "mode" => "rb",
                "unread_bytes" => 0,
                "seekable" => false,
                "uri" => "php://stdin",
            ],
        ];
    }
}
