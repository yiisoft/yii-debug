<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use JetBrains\PhpStorm\ArrayShape;
use Traversable;
use Yiisoft\Validator\Result;

final class ValidatorCollector implements ValidatorCollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private array $validations = [];

    public function getCollected(): array
    {
        return $this->validations;
    }

    public function collect(mixed $value, iterable $rules, Result $result): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->validations[] = [
            'value' => $value,
            'rules' => $rules instanceof Traversable ? iterator_to_array($rules, true) : (array) $rules,
            'result' => $result->isValid(),
            'errors' => $result->getErrors(),
        ];
    }

    private function reset(): void
    {
        $this->validations = [];
    }

    #[ArrayShape([
        'validator.count' => "int",
        'validator.count_valid' => "int",
        'validator.count_invalid' => "int",
    ])]
    public function getIndexData(): array
    {
        $count = count($this->validations);
        $countValid = count(array_filter($this->validations, fn (array $data) => $data['result']));
        $countInvalid = $count - $countValid;

        return [
            'validator.count' => $count,
            'validator.count_valid' => $countValid,
            'validator.count_invalid' => $countInvalid,
        ];
    }
}
