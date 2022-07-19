<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Assets\AssetBundle;

final class AssetCollector implements CollectorInterface, IndexCollectorInterface
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
            'asset.bundles.total' => count($this->assetBundles),
        ];
    }

    private function reset(): void
    {
        $this->assetBundles = [];
    }
}
