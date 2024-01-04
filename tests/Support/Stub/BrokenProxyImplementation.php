<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Support\Stub;

class BrokenProxyImplementation implements Interface1
{
    public function __construct(private Interface1 $decorated)
    {
        throw new \Exception('Broken proxy');
    }
}
