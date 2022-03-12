<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Proxy;

use Yiisoft\Assets\AssetBundle;
use Yiisoft\Assets\AssetLoaderInterface;
use Yiisoft\Yii\Debug\Collector\AssetCollector;

final class AssetLoaderInterfaceProxy implements AssetLoaderInterface
{
    public function __construct(
        private AssetLoaderInterface $assetLoader,
        private AssetCollector $assetCollector
    ) {
    }

    public function getAssetUrl(AssetBundle $bundle, string $assetPath): string
    {
        return $this->assetLoader->getAssetUrl($bundle, $assetPath);
    }

    public function loadBundle(string $name, array $config = []): AssetBundle
    {
        $bundle = $this->assetLoader->loadBundle($name, $config);

        $this->assetCollector->collect($bundle);
        return $bundle;
    }
}
