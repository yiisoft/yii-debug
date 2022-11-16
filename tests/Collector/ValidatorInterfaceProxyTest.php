<?php
declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use PHPUnit\Framework\TestCase;
use Yiisoft\Translator\Translator;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;
use Yiisoft\Yii\Debug\Collector\ValidatorCollector;
use Yiisoft\Yii\Debug\Collector\ValidatorInterfaceProxy;

final class ValidatorInterfaceProxyTest extends TestCase
{
    public function testBase(): void
    {
        $validator = new Validator(new SimpleRuleHandlerContainer(), new Translator());
        $collector = new ValidatorCollector();

        $proxy = new ValidatorInterfaceProxy($validator, $collector);

        $collector->startup();
        $proxy->validate(1, [new Number(min: 7)]);

        $this->assertSame(
            ['validator' => ['total' => 1, 'valid' => 0, 'invalid' => 1]],
            $collector->getIndexData()
        );
    }
}
