<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector\Web;

use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;
use Yiisoft\Yii\Debug\Collector\SummaryCollectorInterface;

final class IdentityCollector implements SummaryCollectorInterface
{
    use CollectorTrait;

    private array $identities = [];

    public function getCollected(): array
    {
        return $this->identities;
    }

    public function collect(?IdentityInterface $identity): void
    {
        if (!$this->isActive()) {
            return;
        }

        if ($identity === null) {
            return;
        }

        $this->identities[] = [
            'id' => $identity->getId(),
            'class' => $identity::class,
        ];
    }

    private function reset(): void
    {
        $this->identities = [];
    }

    public function getSummary(): array
    {
        $lastIdentity = end($this->identities);
        return [
            'identity' => [
                'lastId' => is_array($lastIdentity) ? $lastIdentity['id'] : null,
                'total' => count($this->identities),
            ],
        ];
    }
}
