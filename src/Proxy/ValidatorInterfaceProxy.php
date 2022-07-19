<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Proxy;

use Yiisoft\Validator\Result;
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

        $this->collector->collect(
            $data,
            $rules,
            $result,
        );

        return $result;
    }
}
