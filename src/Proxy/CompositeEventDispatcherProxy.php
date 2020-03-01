<?php

namespace Yiisoft\Yii\Debug\Proxy;

final class CompositeEventDispatcherProxy extends EventDispatcherInterfaceProxy
{
    public function attach(object $event)
    {
        $this->collector->collect($event);

        return $this->dispatcher->attach($event);
    }
}
