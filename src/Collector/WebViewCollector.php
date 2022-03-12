<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\View\Event\WebView\AfterRender;

final class WebViewCollector implements CollectorInterface
{
    use CollectorTrait;

    private array $view = [];

    public function getCollected(): array
    {
        return $this->view;
    }

    public function collect(AfterRender $event): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->view[] = [
            'output' => $event->getResult(),
            'file' => $event->getFile(),
            'parameters' => $event->getParameters(),
        ];
    }

    private function reset(): void
    {
        $this->view = [];
    }
}
