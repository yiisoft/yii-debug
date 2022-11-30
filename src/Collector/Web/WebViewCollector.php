<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector\Web;

use Yiisoft\View\Event\WebView\AfterRender;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;

final class WebViewCollector implements CollectorInterface
{
    use CollectorTrait;

    private array $renders = [];

    public function getCollected(): array
    {
        return $this->renders;
    }

    public function collect(AfterRender $event): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->renders[] = [
            'output' => $event->getResult(),
            'file' => $event->getFile(),
            'parameters' => $event->getParameters(),
        ];
    }

    private function reset(): void
    {
        $this->renders = [];
    }
}
