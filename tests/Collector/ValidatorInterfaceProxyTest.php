<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\ValidatorCollector;

final class ValidatorInterfaceProxyTest extends AbstractCollectorTestCase
{
    /**
     * @param CollectorInterface|ValidatorCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(1, (new Result())->addError('Too low', ['arg1' => 'v1']), [new Number(min: 7)]);
        $collector->collect(10, new Result(), [new Number(min: 7)]);
    }

    protected function getCollector(): CollectorInterface
    {
        return new ValidatorCollector();
    }

    protected function checkCollectedData(array $data): void
    {
        parent::checkCollectedData($data);

        $this->assertEquals(
            [
                [
                    'value' => 1,
                    'rules' => [
                        new Number(min: 7),
                    ],
                    'result' => false,
                    'errors' => [
                        new Error('Too low', ['arg1' => 'v1']),
                    ],
                ],
                [
                    'value' => 10,
                    'rules' => [
                        new Number(min: 7),
                    ],
                    'result' => true,
                    'errors' => [],
                ],
            ],
            $data
        );
    }

    protected function checkSummaryData(array $data): void
    {
        parent::checkSummaryData($data);

        $this->assertEquals(
            ['total' => 2, 'valid' => 1, 'invalid' => 1],
            $data['validator']
        );
    }
}
