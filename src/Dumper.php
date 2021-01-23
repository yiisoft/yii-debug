<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug;

use Closure;
use Yiisoft\VarDumper\ClosureExporter;

final class Dumper
{
    /**
     * @var mixed Variable to dump.
     */
    private $variable;

    private array $objects = [];

    private static ?ClosureExporter $closureExporter = null;

    /**
     * @param mixed $variable Variable to dump.
     */
    private function __construct($variable)
    {
        $this->variable = $variable;
    }

    /**
     * @param mixed $variable Variable to dump.
     *
     * @return static An instance containing variable to dump.
     */
    public static function create($variable): self
    {
        return new self($variable);
    }

    /**
     * Export variable as JSON.
     *
     * @param int $depth Maximum depth that the dumper should go into the variable.
     * @param bool $format Whatever to format exported code.
     *
     * @return string JSON string.
     */
    public function asJson(int $depth = 50, bool $format = false): string
    {
        return $this->asJsonInternal($this->variable, $format, $depth, 0);
    }

    /**
     * Export variable as JSON summary of topmost items.
     *
     * @param int $depth Maximum depth that the dumper should go into the variable.
     * @param bool $prettyPrint Whatever to format exported code.
     *
     * @return string JSON string containing summary.
     */
    public function asJsonObjectsMap(int $depth = 50, bool $prettyPrint = false): string
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
            if (in_array($variable, $this->objects, true)) {
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

    private function asJsonInternal($variable, bool $format, int $depth, int $objectCollapseLevel)
    {
        $options = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE;

        if ($format) {
            $options |= JSON_PRETTY_PRINT;
        }

        return json_encode($this->dumpNested($variable, $depth, $objectCollapseLevel), $options);
    }

    private function dumpNested($variable, int $depth, int $objectCollapseLevel)
    {
        $this->buildObjectsCache($variable, $depth);
        return $this->dumpNestedInternal($variable, $depth, 0, $objectCollapseLevel);
    }

    private function getObjectProperties($var): array
    {
        if ('__PHP_Incomplete_Class' !== get_class($var) && method_exists($var, '__debugInfo')) {
            $var = $var->__debugInfo();
        }

        return (array)$var;
    }

    private function dumpNestedInternal($var, int $depth, int $level, int $objectCollapseLevel = 0)
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
                if ($depth <= $level) {
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
        return get_class($object) . '#' . spl_object_id($object);
    }

    private function normalizeProperty(string $property): string
    {
        $property = str_replace("\0", '::', trim($property));

        if (strpos($property, '*::') === 0) {
            return 'protected $' . substr($property, 3);
        }

        if (($pos = strpos($property, '::')) !== false) {
            return 'private $' . substr($property, $pos + 2);
        }

        return 'public $' . $property;
    }

    private function getResourceDescription($resource)
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
     * @return string
     * @throws \ReflectionException
     *
     */
    private function exportClosure(Closure $closure): string
    {
        if (self::$closureExporter === null) {
            self::$closureExporter = new ClosureExporter();
        }

        return self::$closureExporter->export($closure);
    }

}
