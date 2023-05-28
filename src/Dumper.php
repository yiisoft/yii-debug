<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug;

use Closure;
use JetBrains\PhpStorm\Pure;
use Yiisoft\VarDumper\ClosureExporter;

final class Dumper
{
    private array $objects = [];

    private static ?ClosureExporter $closureExporter = null;

    /**
     * @param mixed $variable Variable to dump.
     */
    private function __construct(private mixed $variable, private array $excludedClasses = [])
    {
    }

    /**
     * @param mixed $variable Variable to dump.
     *
     * @return self An instance containing variable to dump.
     */
    #[Pure]
    public static function create(mixed $variable, array $excludedClasses = []): self
    {
        return new self($variable, $excludedClasses);
    }

    /**
     * Export variable as JSON.
     *
     * @param int $depth Maximum depth that the dumper should go into the variable.
     * @param bool $format Whatever to format exported code.
     *
     * @return bool|string JSON string.
     */
    public function asJson(int $depth = 50, bool $format = false): string|bool
    {
        return $this->asJsonInternal($this->variable, $format, $depth, 0);
    }

    /**
     * Export variable as JSON summary of topmost items.
     *
     * @param int $depth Maximum depth that the dumper should go into the variable.
     * @param bool $prettyPrint Whatever to format exported code.
     *
     * @return bool|string JSON string containing summary.
     */
    public function asJsonObjectsMap(int $depth = 50, bool $prettyPrint = false): string|bool
    {
        $this->buildObjectsCache($this->variable, $depth);

        return $this->asJsonInternal($this->objects, $prettyPrint, $depth, 1);
    }

    private function buildObjectsCache($variable, int $depth, int $level = 0): void
    {
        if ($depth <= $level) {
            return;
        }
        if (is_object($variable)) {
            if (in_array($variable, $this->objects, true)
                || in_array($variable::class, $this->excludedClasses, true)) {
                return;
            }
            $this->objects[] = $variable;
            $variable = $this->getObjectProperties($variable);
        }
        if (is_array($variable)) {
            foreach ($variable as $value) {
                $this->buildObjectsCache($value, $depth, $level + 1);
            }
        }
    }

    private function asJsonInternal($variable, bool $format, int $depth, int $objectCollapseLevel): string|bool
    {
        $options = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE;

        if ($format) {
            $options |= JSON_PRETTY_PRINT;
        }

        return json_encode($this->dumpNested($variable, $depth, $objectCollapseLevel), $options);
    }

    private function dumpNested($variable, int $depth, int $objectCollapseLevel): mixed
    {
        $this->buildObjectsCache($variable, $depth);
        return $this->dumpNestedInternal($variable, $depth, 0, $objectCollapseLevel);
    }

    private function getObjectProperties($var): array
    {
        if (\__PHP_Incomplete_Class::class !== $var::class && method_exists($var, '__debugInfo')) {
            $var = $var->__debugInfo();
        }

        return (array)$var;
    }

    private function dumpNestedInternal($var, int $depth, int $level, int $objectCollapseLevel = 0): mixed
    {
        $output = $var;

        switch (gettype($var)) {
            case 'array':
                if ($depth <= $level) {
                    return 'array [...]';
                }

                $output = [];
                foreach ($var as $key => $value) {
                    $keyDisplay = str_replace("\0", '::', trim((string)$key));
                    $output[$keyDisplay] = $this->dumpNestedInternal($value, $depth, $level + 1, $objectCollapseLevel);
                }

                break;
            case 'object':
                $objectDescription = $this->getObjectDescription($var);
                if ($depth <= $level || in_array($var::class, $this->excludedClasses, true)) {
                    $output = $objectDescription . ' (...)';
                    break;
                }

                if ($var instanceof Closure) {
                    $output = [$objectDescription => $this->exportClosure($var)];
                    break;
                }

                if ($objectCollapseLevel < $level && in_array($var, $this->objects, true)) {
                    $output = 'object@' . $objectDescription;
                    break;
                }

                $output = [];
                $properties = $this->getObjectProperties($var);
                if (empty($properties)) {
                    $output[$objectDescription] = '{stateless object}';
                    break;
                }
                foreach ($properties as $key => $value) {
                    $keyDisplay = $this->normalizeProperty((string)$key);
                    /**
                     * @psalm-suppress InvalidArrayOffset
                     */
                    $output[$objectDescription][$keyDisplay] = $this->dumpNestedInternal(
                        $value,
                        $depth,
                        $level + 1,
                        $objectCollapseLevel
                    );
                }

                break;
            case 'resource':
            case 'resource (closed)':
                $output = $this->getResourceDescription($var);
                break;
        }

        return $output;
    }

    private function getObjectDescription(object $object): string
    {
        return $object::class . '#' . spl_object_id($object);
    }

    private function normalizeProperty(string $property): string
    {
        $property = str_replace("\0", '::', trim($property));

        if (str_starts_with($property, '*::')) {
            return 'protected $' . substr($property, 3);
        }

        if (($pos = strpos($property, '::')) !== false) {
            return 'private $' . substr($property, $pos + 2);
        }

        return 'public $' . $property;
    }

    private function getResourceDescription($resource): array|string
    {
        $type = get_resource_type($resource);
        if ($type === 'stream') {
            $desc = stream_get_meta_data($resource);
        } else {
            $desc = '{resource}';
        }

        return $desc;
    }

    /**
     * Exports a {@see \Closure} instance.
     *
     * @param Closure $closure Closure instance.
     *
     * @throws \ReflectionException
     */
    private function exportClosure(Closure $closure): string
    {
        if (self::$closureExporter === null) {
            self::$closureExporter = new ClosureExporter();
        }

        return self::$closureExporter->export($closure);
    }
}
