<?php

namespace Yiisoft\Yii\Debug\Proxy;

trait ProxyTrait
{
    private ?object $currentError = null;

    protected function getCurrentResultStatus(): string
    {
        return $this->currentError === null ? 'success' : 'failed';
    }

    protected function repeatError(object $error): void
    {
        $this->currentError = $error;
        $errorClass = get_class($error);
        throw new $errorClass($error->getMessage());
    }

    protected function resetCurrentError(): void
    {
        $this->currentError = null;
    }

    protected function getCurrentError(): ?object
    {
        return $this->currentError;
    }
}
