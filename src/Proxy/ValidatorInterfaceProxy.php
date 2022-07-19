<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Proxy;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\Debug\Collector\ValidatorCollectorInterface;

final class ValidatorInterfaceProxy implements ValidatorInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private ValidatorCollectorInterface $collector,
    ) {
    }

    public function validate(mixed $data, iterable $rules = []): Result
    {
        $result = $this->validator->validate($data, $rules);

        if ($data instanceof RulesProviderInterface) {
            $explicitRules = $rules;
            $rules = (array) $data->getRules();

            foreach ($explicitRules as $key => $value) {
                $rules[$key] = $value;
            }
        }

        $this->collector->collect(
            $data,
            $rules,
            $result,
        );

        return $result;
    }
}
