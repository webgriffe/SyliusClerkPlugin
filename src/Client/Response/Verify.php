<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Client\Response;

final readonly class Verify
{
    public function __construct(
        public string $status,
        public string $tokenPayload,
    ) {
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTokenPayload(): string
    {
        return $this->tokenPayload;
    }

    public function isValid(): bool
    {
        return $this->getStatus() === 'ok';
    }
}
