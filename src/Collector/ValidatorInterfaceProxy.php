<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

final class ValidatorInterfaceProxy implements ValidatorInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private ValidatorCollector $collector,
    ) {
    }

    public function validate(
        mixed $data,
        iterable|object|string|null|callable $rules = null,
        ?ValidationContext $context = null
    ): Result {
        $result = $this->validator->validate($data, $rules, $context);

        if ($rules === null && $data instanceof RulesProviderInterface) {
            $rules = (array) $data->getRules();
        }

        $this->collector->collect(
            $data,
            $result,
            $rules,
        );

        return $result;
    }
}
