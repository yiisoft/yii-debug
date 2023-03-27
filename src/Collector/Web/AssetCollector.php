<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector\Web;

use Yiisoft\Assets\AssetBundle;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;
use Yiisoft\Yii\Debug\Collector\IndexCollectorInterface;

final class AssetCollector implements IndexCollectorInterface
{
    use CollectorTrait;

    private array $assetBundles = [];

    public function getCollected(): array
    {
        return $this->assetBundles;
    }

    public function collect(AssetBundle $assetBundle): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->assetBundles[] = $assetBundle;
    }

    public function getIndexData(): array
    {
        return [
            'asset' => [
                'bundles' => [
                    'total' => count($this->assetBundles),
                ],
            ],
        ];
    }

    private function reset(): void
    {
        $this->assetBundles = [];
    }
}
