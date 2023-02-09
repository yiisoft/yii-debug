<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\ValidatorCollector;

final class ValidatorAbstractCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param CollectorInterface|ValidatorCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $ruleNumber = new Number(min: 200);
        $result = new Result();
        $result->addError($ruleNumber->getLessThanMinMessage());

        $collector->collect(123, $result, [$ruleNumber]);
    }

    protected function getCollector(): CollectorInterface
    {
        return new ValidatorCollector();
    }
}
