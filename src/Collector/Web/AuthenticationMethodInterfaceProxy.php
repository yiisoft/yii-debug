<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector\Web;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Auth\AuthenticationMethodInterface;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Yii\Debug\Collector\IdentityCollector;

final class AuthenticationMethodInterfaceProxy implements AuthenticationMethodInterface
{
    public function __construct(private AuthenticationMethodInterface $decorated, private IdentityCollector $collector)
    {
    }

    public function authenticate(ServerRequestInterface $request): ?IdentityInterface
    {
        $identity = null;
        try {
            $identity = $this->decorated->authenticate($request);
        } finally {
            $this->collector->collect($identity);
        }
        return $identity;
    }

    public function challenge(ResponseInterface $response): ResponseInterface
    {
        return $this->decorated->challenge($response);
    }
}
